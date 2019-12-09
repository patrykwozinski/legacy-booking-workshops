<?php
declare(strict_types=1);

namespace App\Modules\Reservations\Domain;


use App\Modules\Shared\Domain\Uuid;
use DateTime;

final class CannotReserveException extends \Exception
{
    public static function forDoctor(Uuid $doctorId): self
    {
        return new self(sprintf('Cannot reserve for doctor: %s', $doctorId));
    }

    public static function whenDateFromThePast(Uuid $doctorId, DateTime $date): self
    {
        $message = sprintf('Cannot reserve date when is from the past for doctor: %s and date: %s', $doctorId, $date->format(DateTime::ATOM));

        return new self($message);
    }
}
