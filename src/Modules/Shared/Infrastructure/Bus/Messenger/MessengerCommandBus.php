<?php

namespace App\Modules\Shared\Infrastructure\Bus\Messenger;

use App\Modules\Shared\Application\Bus\CommandBusInterface;
use App\Modules\Shared\Application\Bus\CommandInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerCommandBus implements CommandBusInterface
{
	/**
	 * @var MessageBusInterface
	 */
	private $bus;

	public function __construct(MessageBusInterface $bus)
	{
		$this->bus = $bus;
	}

	public function dispatch(CommandInterface $command): void
	{
		$this->bus->dispatch($command);
	}
}
