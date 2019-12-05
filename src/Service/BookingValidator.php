<?php
declare(strict_types=1);

namespace App\Service;


class BookingValidator
{
    /**
     * @param array $booking
     *
     * @return bool|string
     */
    public function checkIfValid(array $booking)
    {
        if ($booking['ok'] ?? false) {
            return true;
        }

        return isset($booking['errors']['dateFromThePast']) ? 'given date is from the past' : '';
    }
}
