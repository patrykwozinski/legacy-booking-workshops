<?php

namespace App\Modules\Shared\Domain;

abstract class AggregateRoot
{
	/** @var object[] */
	private $recordedEvents = [];

	/**
	 * @return object[]
	 */
	final public function pullEvents(): array
	{
		$events               = $this->recordedEvents;
		$this->recordedEvents = [];

		return $events;
	}

	final protected function recordThat(object $event): void
	{
		$this->recordedEvents[] = $event;
	}
}
