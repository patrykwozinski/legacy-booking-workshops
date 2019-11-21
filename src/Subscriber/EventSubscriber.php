<?php

namespace App\Subscriber;

use App\Entity\Doctor as DoctorEntity;
use App\Event\BookedEvent;
use Doctrine\Common\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Twig\Environment;

class EventSubscriber
{
	protected $mailer;
	protected $twig;
	protected $em;

	public function __construct(\Swift_Mailer $mailer, Environment $twig, ManagerRegistry $registry)
	{
		$this->mailer = $mailer;
		$this->twig   = $twig;
		$this->em     = $registry->getManager();
	}

	public function onBookedEvent(BookedEvent $event)
	{
		/** @var DoctorEntity $doctor */
		$doctor = $this->em->getRepository(DoctorEntity::class)->find(Uuid::fromString($event->doctor->getId()));

		$message = (new \Swift_Message('Booking confirmation'))
			->setFrom('info@docplanner.com')
			->setTo('radek.baczynski@docplanner.com')
			->setBody(
				$this->twig->render(
					'booked.html.twig',
					['date' => $event->date, 'doctor' => $doctor]
				),
				'text/html'
			);

		$this->mailer->send($message);
	}
}
