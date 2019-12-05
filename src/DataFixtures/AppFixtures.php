<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $booking = new Booking();
        $booking->setDoctorId(Uuid::fromString('0499b62c-a8da-4431-93c1-7ce97c2c78aa'));
        $booking->setDate(new \DateTime('2019-01-02 12:00:00'));
        $booking->setPatient('Jan Kowalski');
        $manager->persist($booking);

        $manager->flush();
    }
}
