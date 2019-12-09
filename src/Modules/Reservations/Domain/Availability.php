<?php
declare(strict_types=1);


namespace App\Modules\Reservations\Domain;


use App\Modules\Shared\Domain\Uuid;
use DateTime;

interface Availability
{
    public function isAvailable(Uuid $doctorId, DateTime $time): bool;

    /**
     * @param Uuid $doctorId
     * @param DateTime $time
     *
     * @throws CannotReserveException
     */
    public function reserve(Uuid $doctorId, DateTIme $time): void;
}
