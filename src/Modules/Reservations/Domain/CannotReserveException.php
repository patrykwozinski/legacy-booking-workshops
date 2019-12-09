<?php
declare(strict_types=1);

namespace App\Modules\Reservations\Domain;


use App\Modules\Shared\Domain\Uuid;

final class CannotReserveException extends \Exception
{
    public static function forDoctor(Uuid $doctorId): self
    {
        return new self(sprintf('Cannot reserve for doctor: %s', $doctorId));
    }
}
