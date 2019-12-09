<?php

namespace App\Modules\Reservations\Application\Command;

use App\Modules\Reservations\Domain\Availability;
use App\Modules\Reservations\Domain\ReservationId;
use App\Modules\Reservations\Domain\Reservations;
use App\Modules\Shared\Application\Bus\CommandHandlerInterface;
use App\Modules\Shared\Application\Bus\EventPublisher;

class CancelReservationHandler implements CommandHandlerInterface
{
    /**
     * @var Reservations
     */
    private $reservations;
    /**
     * @var Availability
     */
    private $availability;
    /**
     * @var EventPublisher
     */
    private $eventPublisher;

    public function __construct(Reservations $reservations, Availability $availability, EventPublisher $eventPublisher)
    {
        $this->reservations = $reservations;
        $this->eventPublisher = $eventPublisher;
        $this->availability = $availability;
    }

    public function __invoke(CancelReservationCommand $command)
    {
        $reservation = $this->reservations->find(new ReservationId($command->getId()));
        $reservation->cancel();

        $this->availability->cancel($reservation->doctorId(), $reservation->date());
        $this->reservations->remove($reservation);
        $this->eventPublisher->record(...$reservation->pullEvents());
    }
}
