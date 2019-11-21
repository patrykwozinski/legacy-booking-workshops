<?php
declare(strict_types=1);

namespace App\Entity;


use DateTime;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

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
     * @var Doctor
     *
     * @ManyToOne(targetEntity="Doctor", inversedBy="bookings")
     * @JoinColumn(name="doctor_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $doctor;

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

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function setDoctor(Doctor $doctor): void
    {
        $this->doctor = $doctor;
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
}
