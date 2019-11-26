<?php
declare(strict_types=1);

namespace App\SDK\AvailabilityApiClient;


use App\SDK\AvailabilityApiClient\IO\Availability;
use App\SDK\AvailabilityApiClient\IO\Doctor;
use App\SDK\AvailabilityApiClient\IO\Duration;

final class AvailabilityApiClient implements AvailabilityApiClientInterface
{
	public function getAvailabilityInformation(Doctor $doctor, \DateTimeImmutable $when): Availability
	{
		return new Availability(Duration::inMinutes(30), true, $when, false);
	}

	public function reserve(Doctor $doctor, \DateTimeImmutable $when): void
	{
		// ...
	}

	public function cancelReservation(Doctor $doctor, \DateTimeImmutable $when): void
	{
		// ...
	}
}
