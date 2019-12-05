<?php
declare(strict_types=1);

namespace App\Service;

use DateTime;

class BookingHelper
{
    public function create(string $date, string $doctorId, ?string $patient): array
    {
        $errors = [];
        $date = new DateTime($date);


        if ($date < new DateTime('now')) {
            $errors['dateFromThePast'] = true;
        }

        if ([] !== $errors) {
            return array_merge(['ok' => false], ['errors' => $errors]);
        }

        return array_merge(['ok' => true], [
            'date' => $date,
            'doctorId' => $doctorId,
            'patient' => $patient,
        ]);
    }
}
