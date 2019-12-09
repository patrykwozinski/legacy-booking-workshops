<?php

namespace App\Modules\Reservations\Application\Command;

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
	 * @var EventPublisher
	 */
	private $eventPublisher;

	/**
	 * CreateReservationHandler constructor.
	 *
	 * @param Reservations $reservations
	 */
	public function __construct(Reservations $reservations, EventPublisher $eventPublisher)
	{
		$this->reservations   = $reservations;
		$this->eventPublisher = $eventPublisher;
	}

	public function __invoke(CancelReservationCommand $command)
	{
		$reservation = $this->reservations->find(new ReservationId($command->getId()));

		$reservation->cancel();
		$this->reservations->remove($reservation);
		$this->eventPublisher->record(...$reservation->pullEvents());
	}
}
