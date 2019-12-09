<?php
declare(strict_types=1);

namespace App\Modules\Reservations\Infrastructure\SdkAvailability;


use App\Modules\Reservations\Domain\Availability;
use App\Modules\Reservations\Domain\CannotReserveException;
use App\Modules\Reservations\Domain\ReservationResult;
use App\Modules\Shared\Domain\Uuid;
use App\SDK\AvailabilityApiClient\AvailabilityApiClientInterface;
use App\SDK\AvailabilityApiClient\IO\Doctor as SdkDoctor;
use DateTime;

final class SdkAvailability implements Availability
{
    /** @var AvailabilityApiClientInterface */
    private $apiClient;

    public function __construct(AvailabilityApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function isAvailable(Uuid $doctorId, DateTime $time): bool
    {
        $availability = $this->apiClient->getAvailabilityInformation(new SdkDoctor((string)$doctorId), $time);

        return $availability->exists() && false === $availability->reserved();
    }

    /**
     * @param Uuid $doctorId
     * @param DateTime $time
     *
     * @throws CannotReserveException
     */
    public function reserve(Uuid $doctorId, DateTime $time): void
    {
        try {
            $this->apiClient->reserve(new SdkDoctor((string)$doctorId), $time);
        } catch (\Exception $exception) {
            throw CannotReserveException::forDoctor($doctorId);
        }
    }
}
