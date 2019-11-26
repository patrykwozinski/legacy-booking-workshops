<?php
declare(strict_types=1);

namespace App\SDK\AvailabilityApiClient;


use App\SDK\AvailabilityApiClient\IO\Availability;
use App\SDK\AvailabilityApiClient\IO\Doctor;

interface AvailabilityApiClientInterface
{
    public function getAvailabilityInformation(Doctor $doctor, \DateTimeImmutable $when): Availability;
	public function reserve(Doctor $doctor, \DateTimeImmutable $when): void;
	public function cancelReservation(Doctor $doctor, \DateTimeImmutable $when): void;
}
