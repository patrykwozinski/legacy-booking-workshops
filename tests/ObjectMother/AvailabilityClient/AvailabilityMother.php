<?php
declare(strict_types=1);

namespace App\Tests\ObjectMother\AvailabilityClient;


use App\SDK\AvailabilityApiClient\IO\Availability;
use App\SDK\AvailabilityApiClient\IO\Duration;
use DateTimeImmutable;

final class AvailabilityMother
{
    /** @var Duration */
    private $duration;
    /** @var bool */
    private $exists;
    /** @var DateTimeImmutable */
    private $startFrom;
    /** @var bool */
    private $reserved;

    private function __construct()
    {
        $this->duration = Duration::inMinutes(60);
        $this->exists = false;
        $this->startFrom = new DateTimeImmutable('now');
        $this->reserved = false;
    }

    public static function make(): self
    {
        return new self;
    }

    public function existing(): self
    {
        $this->exists = true;

        return $this;
    }

    public function missing(): self
    {
        $this->exists = false;

        return $this;
    }

    public function reserved(): self
    {
        $this->reserved = true;

        return $this;
    }

    public function notReserved(): self
    {
        $this->reserved = false;

        return $this;
    }

    public function get(): Availability
    {
        return new Availability($this->duration, $this->exists, $this->startFrom, $this->reserved);
    }
}
