<?php
declare(strict_types=1);

namespace App\Modules\Reservations\Domain;


use App\Modules\Shared\Domain\Uuid;

final class DoctorNotAvailableException extends \Exception
{
    public static function withId(Uuid $doctorId): self
    {
        return new self(sprintf('Given doctor is not available: %s', $doctorId));
    }
}
