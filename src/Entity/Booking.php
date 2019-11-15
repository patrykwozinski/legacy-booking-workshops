<?php
declare(strict_types=1);

namespace App\Entity;


class Booking
{
    /** @var Doctor */
    private $doctor;

    public function setDoctor(Doctor $doctor): void
    {
        $this->doctor = $doctor;
    }
}
