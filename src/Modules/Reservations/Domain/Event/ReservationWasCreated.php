<?php

namespace App\Modules\Reservations\Domain\Event;

use App\Modules\Reservations\Domain\ReservationId;

class ReservationWasCreated
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
