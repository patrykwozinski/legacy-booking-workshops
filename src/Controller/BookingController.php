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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends AbstractController
{
    public function bookVisit(Request $request): Response
    {
        $date = $request->get('date');
        $doctorId = $request->get('doctorId');

        /** @var EntityManager $em */
        $em = $this->get('doctrine');
        /** @var DoctorEntity $doctor */
        $doctor = $em->getRepository(DoctorEntity::class)->find($doctorId ?? '');

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
            return new Response('Given date does not exists in calendar or is reserved');
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

			$event = new BookedEvent();
			$event->date = $date;
			$event->doctorId = $doctorId;

			/** @var EventDispatcherInterface $dispatcher */
			$dispatcher = $this->get(EventDispatcherInterface::class);
			$dispatcher->dispatch($event);

            return new Response('Booked!');
        }

        return new Response('Cannot book visit with errors: ' . $bookingStatus);
    }
}
