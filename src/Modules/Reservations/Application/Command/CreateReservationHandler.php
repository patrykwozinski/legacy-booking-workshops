<?php
declare(strict_types=1);

namespace App\Modules\Reservations\Application\Command;


use App\Modules\Reservations\Domain\CannotReserveException;
use App\Modules\Reservations\Domain\DoctorNotAvailableException;
use App\Modules\Reservations\Domain\ReservationFactory;
use App\Modules\Reservations\Domain\ReservationId;
use App\Modules\Reservations\Domain\Reservations;
use App\Modules\Shared\Application\Bus\CommandHandlerInterface;
use App\Modules\Shared\Application\Bus\EventPublisher;
use App\Modules\Shared\Domain\Uuid;
use DateTime;

final class CreateReservationHandler implements CommandHandlerInterface
{
    /**
     * @var ReservationFactory
     */
    private $reservationFactory;
    /**
     * @var Reservations
     */
    private $reservations;
    /**
     * @var EventPublisher
     */
    private $eventPublisher;

    public function __construct(ReservationFactory $reservationFactory, Reservations $reservations, EventPublisher $eventPublisher)
    {
        $this->reservationFactory = $reservationFactory;
        $this->reservations = $reservations;
        $this->eventPublisher = $eventPublisher;
    }

    /**
     * @param CreateReservationCommand $command
     *
     * @throws CannotReserveException
     * @throws DoctorNotAvailableException
     */
    public function __invoke(CreateReservationCommand $command)
    {
        $reservation = $this->reservationFactory->create(
            new ReservationId($command->getId()),
            new Uuid($command->getDoctorId()),
            $command->getPatient(),
            new DateTime($command->getDate())
        );

        $this->reservations->add($reservation);
        $this->eventPublisher->record(...$reservation->pullEvents());
    }
}
