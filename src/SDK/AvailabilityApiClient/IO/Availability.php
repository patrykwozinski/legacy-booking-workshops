<?php
declare(strict_types=1);

namespace App\SDK\AvailabilityApiClient\IO;


final class Availability
{
	private $duration;
	private $exists;
	private $startFrom;

	public function __construct(Duration $duration, bool $exists, \DateTimeImmutable $startFrom)
	{
		$this->duration  = $duration;
		$this->exists    = $exists;
		$this->startFrom = $startFrom;
	}

	public function duration(): Duration
	{
		return $this->duration;
	}

	public function exists(): bool
	{
		return $this->exists;
	}

	public function startFrom(): \DateTimeImmutable
	{
		return $this->startFrom;
	}
}
