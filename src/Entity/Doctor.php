<?php
declare(strict_types=1);

namespace App\Entity;


class Doctor
{
	private $isPremium;
	private $registeredAt;
	private $isActive;

	public function __construct(bool $isPremium, bool $isActive, \DateTime $registeredAt)
	{
		$this->isPremium    = $isPremium;
		$this->registeredAt = $registeredAt;
		$this->isActive     = $isActive;
	}

	public function getIsPremium(): bool
	{
		return $this->isPremium;
	}

	public function getRegisteredAt(): \DateTime
	{
		return $this->registeredAt;
	}

	public function getIsActive(): bool
	{
		return $this->isActive;
	}
}
