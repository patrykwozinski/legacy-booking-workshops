<?php

namespace App\Modules\Reservations\Application\Command;

use App\Modules\Shared\Application\Bus\CommandInterface;

class CreateReservationCommand implements CommandInterface
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $doctorId;

	/**
	 * @var string
	 */
	private $patient;

	/**
	 * @var string
	 */
	private $date;

	public function __construct(string $id, string $doctorId, string $patient, string $date)
	{
		$this->id       = $id;
		$this->doctorId = $doctorId;
		$this->patient  = $patient;
		$this->date     = $date;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getDoctorId(): string
	{
		return $this->doctorId;
	}

	public function getPatient(): string
	{
		return $this->patient;
	}

	public function getDate(): string
	{
		return $this->date;
	}
}
