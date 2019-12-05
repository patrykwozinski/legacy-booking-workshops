<?php

namespace App\Modules\Shared\Domain;

use App\Modules\Reservations\Domain\Reservation;
use App\Modules\Reservations\Domain\ReservationId;

interface Reservations
{
	public function add(Reservation $reservation): void;

	public function remove(Reservation $reservation): void;

	public function find(ReservationId $reservationId): Reservation;
}
