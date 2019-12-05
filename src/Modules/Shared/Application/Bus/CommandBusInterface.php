<?php

namespace App\Modules\Shared\Application\Bus;

interface CommandBusInterface
{
	public function dispatch(CommandInterface $command);
}
