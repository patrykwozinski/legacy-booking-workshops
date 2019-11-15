<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\Booking;
use App\Entity\Doctor as DoctorEntity;
use App\SDK\AvailabilityApiClient\AvailabilityApiClient;
use App\SDK\AvailabilityApiClient\IO\Doctor as SdkDoctor;
use App\Service\BookingHelper;
use App\Service\BookingValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends Controller
{
    public function bookVisit(Request $request): JsonResponse
    {
        $date = $request->get('date') ?? '';
        $doctorId = $request->get('doctorId') ?? '';

        /** @var EntityManager $em */
        $em = $this->get('doctrine');
        /** @var DoctorEntity $doctor */
        $doctor = $em->getRepository(DoctorEntity::class)->find($doctorId);

        if (!$doctor) {
            return $this->json([
                'message' => 'doctor not found',
                'code' => JsonResponse::HTTP_I_AM_A_TEAPOT,
            ], JsonResponse::HTTP_I_AM_A_TEAPOT);
        }

        /** @var BookingHelper $bookingHelper */
        $bookingHelper = $this->get(BookingHelper::class);
        $booking = $bookingHelper->create($date, $doctor, $request->get('patient'));

        /** @var AvailabilityApiClient $availabilityApi */
        $availabilityApi = $this->get(AvailabilityApiClient::class);
        $availability = $availabilityApi->getAvailabilityInformation(
            new SdkDoctor($doctorId),
            new \DateTimeImmutable($date)
        );

        if (false === $availability->exists() || $availability->reserved()) {
            return $this->json('Given date does not exists in calendar or is reserved');
        }

        /** @var BookingValidator $validator */
        $validator = $this->get(BookingValidator::class);
        $bookingStatus = $validator->checkIfValid($booking);

        if ($bookingStatus) {
            $booking = new Booking;
            $booking->setDoctor($doctor);
            $booking->setPatient($booking['patient']);
            $booking->setDate($booking['date']);
            $em->persist($booking);
            $em->flush();

            return $this->json('Booked!');
        }

        return $this->json('Cannot book visit with errors: ' . $bookingStatus);
    }

    public function getBookings(Request $request): JsonResponse
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine');

        $bookings = $em->getRepository(Booking::class)->findBy([
            'doctor_id' => $request->get('doctor_id'),
        ]);

        $bookings = array_map(function (Booking $booking) {
            return [
                'date' => $booking->getDate()->format('Y-m-d H:i:s'),
                'patient' => $booking->getPatient(),
            ];
        }, $bookings);

        return $this->json([
            'doctor' => $request->get('doctor_id'),
            'bookings' => $bookings,
        ]);
    }
}
