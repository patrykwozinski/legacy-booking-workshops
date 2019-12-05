<?php

namespace App\Modules\Reservations\Application\Command;

use App\Modules\Shared\Application\Bus\CommandInterface;

class CancelReservationCommand implements CommandInterface
{
	/**
	 * @var string
	 */
	private $id;

	public function __construct(string $id)
	{
		$this->id       = $id;
	}

	public function getId(): string
	{
		return $this->id;
	}
}
