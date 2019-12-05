<?php

namespace App\Modules\Shared\Infrastructure\Bus\Messenger;

use App\Modules\Shared\Application\Bus\CommandBusInterface;
use App\Modules\Shared\Application\Bus\EventBusInterface;
use App\Modules\Shared\Domain\Bus\EventInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerEventBus implements EventBusInterface
{
	/**
	 * @var MessageBusInterface
	 */
	private $bus;

	public function __construct(MessageBusInterface $bus)
	{
		$this->bus = $bus;
	}

	public function dispatch(EventInterface $command): void
	{
		$this->bus->dispatch($command);
	}
}
