<?php

namespace App\Modules\Reservations\Domain;

use App\Modules\Reservations\Domain\Event\ReservationWasCanceled;
use App\Modules\Reservations\Domain\Event\ReservationWasCreated;
use App\Modules\Shared\Domain\AggregateRoot;
use App\Modules\Shared\Domain\Uuid;
use DateTime;

class Reservation extends AggregateRoot
{
    /**
     * @var ReservationId
     */
    private $id;

    /**
     * @var Uuid
     */
    private $doctor;

    /**
     * @var string
     */
    private $patient;

    /**
     * @var DateTime
     */
    private $date;

    private function __construct(ReservationId $id, Uuid $doctor, string $patient, DateTime $date)
    {
        $this->id = $id;
        $this->doctor = $doctor;
        $this->patient = $patient;
        $this->date = $date;
    }

    public static function create(ReservationId $id, Uuid $doctor, string $patient, DateTime $date): self
    {
        $reservation = new self($id, $doctor, $patient, $date);
        $reservation->recordThat(new ReservationWasCreated($id));

        return $reservation;
    }

    public function cancel(): void
    {
        $this->recordThat(new ReservationWasCanceled($this->id));
    }

    public function doctorId(): Uuid
    {
        return $this->doctor;
    }

    public function date(): DateTime
    {
        return $this->date;
    }
}
