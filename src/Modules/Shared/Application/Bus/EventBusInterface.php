<?php

namespace App\Modules\Shared\Application\Bus;

use App\Modules\Shared\Domain\Bus\EventInterface;

interface EventBusInterface
{
	public function dispatch(EventInterface $event);
}
