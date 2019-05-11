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
	public function checkIfValidate(array $booking)
	{
		if ($booking['ok'] ?? false)
		{
			return true;
		}

		$errors   = $booking['errors'] ?? [];
		$failedBy = [];

		if (isset($errors['doctorVacation']))
		{
			$failedBy[] = 'doctor is on vacation';
		}

		if (isset($errors['dateFromThePast']))
		{
			$failedBy[] = 'given date is from the past';
		}
	}
}
