<?php
declare(strict_types=1);

namespace App\SDK\AvailabilityApiClient\IO;

final class Doctor
{
	private $id;

	public function __construct(int $id)
	{
		$this->id = $id;
	}

	public function id(): int
	{
		return $this->id;
	}
}
