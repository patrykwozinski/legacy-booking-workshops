<?php
declare(strict_types=1);

namespace App\Modules\Reservations\Infrastructure\SdkAvailability;


use App\Modules\Reservations\Domain\Availability;
use App\Modules\Reservations\Domain\CannotReserveException;
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
        $availability = $this->apiClient->getAvailabilityInformation($this->doctor($doctorId), $time);

        return $availability->exists() && false === $availability->reserved();
    }

    /**
     * @param Uuid $doctorId
     * @param DateTime $dateTime
     *
     * @throws CannotReserveException
     */
    public function reserve(Uuid $doctorId, DateTime $dateTime): void
    {
        try {
            $this->apiClient->reserve($this->doctor($doctorId), $dateTime);
        } catch (\Exception $exception) {
            throw CannotReserveException::forDoctor($doctorId);
        }
    }

    public function cancel(Uuid $doctorId, DateTime $dateTime): void
    {
        $this->apiClient->cancelReservation($this->doctor($doctorId), $dateTime);
    }

    private function doctor(Uuid $doctorId): SdkDoctor
    {
        return new SdkDoctor((string)$doctorId);
    }
}
