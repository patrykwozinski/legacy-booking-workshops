<?php
declare(strict_types=1);

namespace App\SDK\AvailabilityApiClient;


use App\SDK\AvailabilityApiClient\IO\Availability;
use App\SDK\AvailabilityApiClient\IO\Doctor;
use App\SDK\AvailabilityApiClient\IO\Duration;

final class AvailabilityApiClient
{
	public function getAvailabilityInformation(Doctor $doctor): Availability
	{
		return new Availability(Duration::inMinutes(30));
	}
}
