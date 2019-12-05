<?php

namespace App\Modules\Reservations\Application\Command;

use App\Modules\Reservations\Domain\Reservation;
use App\Modules\Reservations\Domain\ReservationId;
use App\Modules\Shared\Application\Bus\CommandHandlerInterface;
use App\Modules\Shared\Application\Bus\EventPublisher;
use App\Modules\Shared\Domain\Reservations;
use App\Modules\Shared\Domain\Uuid;

class CreateReservationHandler implements CommandHandlerInterface
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

	public function __invoke(CreateReservationCommand $command)
	{
		$reservation = Reservation::create(
			new ReservationId($command->getId()),
			new Uuid($command->getDoctorId()),
			$command->getPatient(),
			new \DateTime($command->getDate())
		);

		$this->reservations->add($reservation);
		$this->eventPublisher->record(...$reservation->pullEvents());
	}
}
