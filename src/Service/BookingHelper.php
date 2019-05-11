<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Doctor;

class BookingHelper
{
	public function create(string $date, int $doctorId, ?string $patient): array
	{
		$doctor = new Doctor(false);

		$response = [
			'ok' => false,
		];

		$monthAgo = (new \DateTime)->modify('-1 month');

		if (true === $doctor->getIsPremium() || $monthAgo <= $doctor->getRegisteredAt())
		{
			return array_merge($response, [
				'date'    => new \DateTime($date),
				'doctor'  => '',
				'patient' => $patient,
			]);
		}

		$errors = [
			'notPremium'         => true,
			'registeredRecently' => true,
		];

		return array_merge($response, $errors);
	}
}
