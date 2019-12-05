<?php
declare(strict_types=1);

namespace App\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Booking
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $patient;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var UuidInterface
     *
     * @ORM\Column(type="uuid", unique=false)
     */
    private $doctorId;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function setPatient(string $patient): void
    {
        $this->patient = $patient;
    }

    public function getPatient(): string
    {
        return $this->patient;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDoctorId(UuidInterface $doctorId): void
    {
        $this->doctorId = $doctorId;
    }

    public function getDoctorId(): UuidInterface
    {
        return $this->doctorId;
    }
}
