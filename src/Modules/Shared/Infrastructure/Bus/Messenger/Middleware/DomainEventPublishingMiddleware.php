<?php

namespace App\Modules\Shared\Infrastructure\Bus\Messenger\Middleware;

use App\Modules\Shared\Application\Bus\EventPublisher;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class DomainEventPublishingMiddleware implements MiddlewareInterface
{
	/** @var EventPublisher */
	private $eventPublisher;

	public function __construct(EventPublisher $eventPublisher)
	{
		$this->eventPublisher = $eventPublisher;
	}

	public function handle(Envelope $envelope, StackInterface $stack): Envelope
	{
		$envelope = $stack->next()->handle($envelope, $stack);
		$this->eventPublisher->publishEvents();

		return $envelope;
	}
}
