<?php
declare(strict_types=1);

namespace App\UI\Controller;


use App\Entity\Booking;
use App\Event\BookedEvent;
use App\Modules\Reservations\Application\Command\CreateReservationCommand;
use App\Modules\Reservations\Domain\DoctorNotAvailableException;
use App\Modules\Shared\Application\Bus\CommandBusInterface;
use App\SDK\AvailabilityApiClient\AvailabilityApiClientInterface;
use App\SDK\AvailabilityApiClient\IO\Doctor as SdkDoctor;
use App\Service\BookingHelper;
use App\Service\BookingValidator;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class BookController extends Controller
{
    /** @var CommandBusInterface */
    private $commandBus;

    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @Route( "/book", methods={"POST"})
     */
    public function bookVisit(Request $request): JsonResponse
    {
        $date = $request->get('date') ?? '';
        $doctorId = $request->get('doctor_id') ?? '';
        $patient = $request->get('patient') ?? '';
        $reservationId = Uuid::uuid4()->toString();

        try {
            $this->commandBus->dispatch(new CreateReservationCommand($reservationId, $doctorId, $patient, $date));

            return new JsonResponse([
                'message' => 'Your reservation was booked',
                'booking_id' => $reservationId,
            ]);
        } catch (\Exception $exception) {
           // elo póki co na wszystko
        }

        /** @var Registry $em */
        $em = $this->get('doctrine');
        /** @var EventDispatcherInterface $ed */
        $ed = $this->get('event_dispatcher');

        /** @var BookingHelper $bookingHelper */
        $bookingHelper = $this->get(BookingHelper::class);
        $booking = $bookingHelper->create($date, $doctorId, $request->get('patient'));

        /** @var AvailabilityApiClientInterface $availabilityApi */
        $availabilityApi = $this->get(AvailabilityApiClientInterface::class);

        $availability = $availabilityApi->getAvailabilityInformation(
            new SdkDoctor($doctorId),
            new DateTime($date)
        );

        if (false === $availability->exists() || $availability->reserved()) {
            return new JsonResponse([
                'message' => 'Given date does not exists in calendar or is reserved',
                'doctor_id' => $doctorId,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        /** @var BookingValidator $validator */
        $validator = $this->get(BookingValidator::class);
        $bookingStatus = $validator->checkIfValid($booking);

        if ($bookingStatus === true) {
            $booking = new Booking;
            $booking->setDoctorId(Uuid::fromString($doctorId));
            $booking->setPatient($patient);
            $booking->setDate(new \DateTime($date));
            $em->getManager()->persist($booking);
            $em->getManager()->flush();

            $availabilityApi->reserve(new SdkDoctor($doctorId), new DateTime($date));

            $event = new BookedEvent;
            $event->date = new \DateTime($date);
            $event->bookingId = $booking->getId();
            $event->doctorId = $doctorId;

            $ed->dispatch($event);

            return new JsonResponse([
                'message' => 'Your reservation was booked',
                'booking_id' => $booking->getId()->toString(),
            ]);
        }

        return new JsonResponse([
            'message' => 'Cannot book visit with errors: ' . $bookingStatus,
            'doctor_id' => $doctorId,
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
