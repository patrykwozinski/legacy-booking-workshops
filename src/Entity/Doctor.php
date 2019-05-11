<?php
declare(strict_types=1);

namespace App\Entity;


class Doctor
{
	private $isPremium;
	private $registeredAt;

	public function __construct(bool $isPremium, \DateTime $registeredAt)
	{
		$this->isPremium    = $isPremium;
		$this->registeredAt = $registeredAt;
	}

	public function getIsPremium(): bool
	{
		return $this->isPremium;
	}

	public function getRegisteredAt(): \DateTime
	{
		return $this->registeredAt;
	}
}
