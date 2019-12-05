<?php

namespace App\Modules\Shared\Application\Bus;

use App\Modules\Shared\Domain\Bus\EventInterface;

interface EventPublisher
{
	public function record(EventInterface ...$events): void;

	public function publishEvents(): void;
}
