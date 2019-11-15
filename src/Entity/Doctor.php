<?php
declare(strict_types=1);

namespace App\Entity;


use DateTime;

class Doctor
{
    /** @var bool */
    private $isPremium;

    /** @var DateTime */
    private $registeredAt;

    /** @var bool */
    private $isActive;

    public function getIsPremium(): bool
    {
        return $this->isPremium;
    }

    public function setIsPremium(bool $isPremium): void
    {
        $this->isPremium = $isPremium;
    }

    public function getRegisteredAt(): DateTime
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(DateTime $dateTime): void
    {
        $this->registeredAt = $dateTime;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}
