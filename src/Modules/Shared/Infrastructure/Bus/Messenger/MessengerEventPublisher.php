<?php

namespace App\Modules\Shared\Infrastructure\Bus\Messenger;

use App\Modules\Shared\Application\Bus\EventPublisher;
use App\Modules\Shared\Application\Bus\EventBusInterface;
use App\Modules\Shared\Domain\Bus\EventInterface;

class MessengerEventPublisher implements EventPublisher
{
	/**
	 * @var EventBusInterface
	 */
	private $bus;

	/**
	 * @var EventInterface[]
	 */
	private $recordedEvents = [];

	public function __construct(EventBusInterface $bus)
	{
		$this->bus = $bus;
	}

	public function record(EventInterface ...$events): void
	{
		foreach ($events as $event) {
			$this->recordedEvents[] = $event;
		}
	}

	public function publishEvents(): void
	{
		$events = $this->recordedEvents;
		$this->recordedEvents = [];
		foreach ($events as $event) {
			$this->bus->dispatch($event);
		}
	}
}
