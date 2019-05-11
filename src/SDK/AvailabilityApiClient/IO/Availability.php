<?php
declare(strict_types=1);

namespace App\SDK\AvailabilityApiClient\IO;


final class Availability
{
	private $duration;
	private $exists;
	private $startFrom;
	private $isReserved;

	public function __construct(Duration $duration, bool $exists, \DateTimeImmutable $startFrom, bool $isReserved)
	{
		$this->duration   = $duration;
		$this->exists     = $exists;
		$this->startFrom  = $startFrom;
		$this->isReserved = $isReserved;
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

	public function reserved(): bool
	{
		return $this->isReserved;
	}
}
