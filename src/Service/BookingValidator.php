<?php
declare(strict_types=1);

namespace App\Service;


class BookingValidator
{
	/**
	 * @param array $booking
	 *
	 * @return bool|string
	 */
	public function checkIfValid(array $booking)
	{
		if ($booking['ok'] ?? false)
		{
			return true;
		}

		$errors   = $booking['errors'] ?? [];
		$failedBy = [];

		if (isset($errors['dateFromThePast']))
		{
			$failedBy[] = 'given date is from the past';
		}

		if (isset($errors['notPremium']))
		{
			$failedBy[] = 'doctor must be premium';
		}

		if (isset($errors['notActive']))
		{
			$failedBy[] = 'doctor must be active';
		}

		return implode(', ', $failedBy);
	}
}
