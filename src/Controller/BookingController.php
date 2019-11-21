<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\Booking;
use App\Entity\Doctor as DoctorEntity;
use App\SDK\AvailabilityApiClient\AvailabilityApiClientInterface;
use App\SDK\AvailabilityApiClient\IO\Doctor as SdkDoctor;
use App\Service\BookingHelper;
use App\Service\BookingValidator;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends Controller
{
    public function bookVisit(Request $request): JsonResponse
    {
        $date = $request->get('date') ?? '';
        $doctorId = $request->get('doctor_id') ?? '';
        $patient = $request->get('patient') ?? '';

        /** @var Registry $em */
        $em = $this->get('doctrine');
        /** @var DoctorEntity $doctor */
        $doctor = $em->getRepository(DoctorEntity::class)->find(Uuid::fromString($doctorId));

        if (!$doctor) {
            return new JsonResponse([
                'message' => 'doctor not found',
            ], JsonResponse::HTTP_I_AM_A_TEAPOT);
        }

        /** @var BookingHelper $bookingHelper */
        $bookingHelper = $this->get(BookingHelper::class);
        $booking = $bookingHelper->create($date, $doctor, $request->get('patient'));

        /** @var AvailabilityApiClientInterface $availabilityApi */
        $availabilityApi = $this->get(AvailabilityApiClientInterface::class);

        $availability = $availabilityApi->getAvailabilityInformation(
            new SdkDoctor($doctorId),
            new DateTimeImmutable($date)
        );

        if (false === $availability->exists() || $availability->reserved()) {
            return new JsonResponse([
                'message' => 'Given date does not exists in calendar or is reserved',
                'doctor_id' => $doctorId,
            ]);
        }

        /** @var BookingValidator $validator */
        $validator = $this->get(BookingValidator::class);
        $bookingStatus = $validator->checkIfValid($booking);

        if ($bookingStatus === true) {
            $booking = new Booking;
            $booking->setDoctor($doctor);
            $booking->setPatient($patient);
            $booking->setDate(new \DateTime($date));
            $em->getManager()->persist($booking);
            $em->getManager()->flush();

            return new JsonResponse([
                'message' => 'Booked!',
                'booking_id' => $booking->getId()->toString(),
            ]);
        }

        return new JsonResponse([
            'message' => 'Cannot book visit with errors: ' . $bookingStatus,
            'doctor_id' => $doctorId,
        ]);
    }

    public function getBookings(Request $request): JsonResponse
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine');

        $bookings = $em->getRepository(Booking::class)->findBy([
            'doctor' => $request->get('doctor_id'),
        ]);

        $bookings = array_map(function (Booking $booking) {
            return [
                'date' => $booking->getDate()->format('Y-m-d H:i:s'),
                'patient' => $booking->getPatient(),
            ];
        }, $bookings);

        return new JsonResponse([
            'doctor' => $request->get('doctor_id'),
            'bookings' => $bookings,
        ]);
    }
}
