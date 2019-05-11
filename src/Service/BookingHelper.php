<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Doctor;

class BookingHelper
{
	public function create(string $date, int $doctorId, ?string $patient): array
	{
		$doctor = new Doctor(false);

		$errors = [];

		$monthAgo = (new \DateTime)->modify('-1 month');

		if (false === $doctor->getIsPremium() && $monthAgo > $doctor->getRegisteredAt())
		{
			$errors['notPremium']         = true;
			$errors['registeredRecently'] = true;
		}

		if (false === $doctor->getIsActive())
		{
			$errors['notActive'] = true;
		}

		if ([] !== $errors)
		{
			return array_merge(['ok' => false], $errors);
		}

		return array_merge(['ok' => true], [
			'date'    => new \DateTime($date),
			'doctor'  => '',
			'patient' => $patient,
		]);
	}
}
