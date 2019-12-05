<?php

namespace App\Subscriber;

use App\Event\BookedEvent;
use Doctrine\Common\Persistence\ManagerRegistry;
use Twig\Environment;

class EventSubscriber
{
    private const DOCTOR_FAKE_EMAIL = 'patryk.wozinski@docplanner.com';
    protected $mailer;
    protected $twig;
    protected $em;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, ManagerRegistry $registry)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $registry->getManager();
    }

    public function onBookedEvent(BookedEvent $event)
    {
        $message = (new \Swift_Message('Booking confirmation'))
            ->setFrom('info@docplanner.com')
            ->setTo(self::DOCTOR_FAKE_EMAIL)
            ->setBody(
                $this->twig->render(
                    'booked.html.twig',
                    ['date' => $event->date, 'doctor' => $event->doctorId]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
