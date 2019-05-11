<?php
declare(strict_types=1);

namespace App\SDK\AvailabilityApiClient\IO;


final class Duration
{
	private $minutes;

	private function __construct(int $minutes)
	{
		$this->minutes = $minutes;
	}

	public function minutes(): int
	{
		return $this->minutes;
	}

	public function seconds(): int
	{
		return $this->minutes * 60;
	}

	public static function inMinutes(int $minutes): self
	{
		return new self($minutes);
	}
}
