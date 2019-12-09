<?php
declare(strict_types=1);

namespace App\Modules\Reservations\Domain;


use App\Modules\Shared\Domain\Uuid;
use DateTime;

final class ReservationFactory
{
    /** @var Availability */
    private $availability;

    public function __construct(Availability $availability)
    {
        $this->availability = $availability;
    }

    /**
     * Creates reservation if it is possible
     *
     * @param ReservationId $id
     * @param Uuid $doctorId
     * @param string $patient
     * @param DateTime $date
     * @return Reservation
     *
     * @throws DoctorNotAvailableException
     * @throws CannotReserveException
     */
    public function create(ReservationId $id, Uuid $doctorId, string $patient, DateTime $date): Reservation
    {
        if (false === $this->availability->isAvailable($doctorId, $date)) {
            throw DoctorNotAvailableException::withId($doctorId);
        }

        $reservation = Reservation::create($id, $doctorId, $patient, $date);

        $this->availability->reserve($doctorId, $date);

        return $reservation;
    }
}
