<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain;

class Uuid
{
	/**
	 * @var \Ramsey\Uuid\UuidInterface
	 */
	protected $uuid;

	public function __construct(?string $uuid = null)
	{
		if ($uuid)
		{
			$this->uuid = \Ramsey\Uuid\Uuid::fromString($uuid);
		}
		else
		{
			$this->uuid = \Ramsey\Uuid\Uuid::uuid4();
		}
	}

	public function __toString()
	{
		return $this->uuid->toString();
	}
}
