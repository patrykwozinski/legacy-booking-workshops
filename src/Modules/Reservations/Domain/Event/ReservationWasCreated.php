<?php

namespace App\Modules\Reservations\Domain\Event;

use App\Modules\Reservations\Domain\ReservationId;
use App\Modules\Shared\Domain\Bus\EventInterface;

class ReservationWasCreated implements EventInterface
{
	/**
	 * @var ReservationId
	 */
	private $id;

	public function __construct(ReservationId $id)
	{
		$this->id = $id;
	}

	public function getId(): ReservationId
	{
		return $this->id;
	}
}
