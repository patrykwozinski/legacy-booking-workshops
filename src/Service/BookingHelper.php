<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Doctor;
use DateTime;

class BookingHelper
{
    public function create(string $date, Doctor $doctor, ?string $patient): array
    {
        $errors = [];
        $date = new DateTime($date);

        if (false === $doctor->getIsPremium()) {
            $errors['notPremium'] = true;
        }

        if (false === $doctor->getIsActive()) {
            $errors['notActive'] = true;
        }

        if ($date < new DateTime('now')) {
            $errors['dateFromThePast'] = true;
        }

        if ([] !== $errors) {
            return array_merge(['ok' => false], ['errors' => $errors]);
        }

        return array_merge(['ok' => true], [
            'date' => $date,
            'doctor' => $doctor,
            'patient' => $patient,
        ]);
    }
}
