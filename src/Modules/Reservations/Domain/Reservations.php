<?php

namespace App\Modules\Reservations\Domain;


interface Reservations
{
	public function add(Reservation $reservation): void;

	public function remove(Reservation $reservation): void;

	public function find(ReservationId $reservationId): Reservation;
}
