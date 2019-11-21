<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Doctor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Ramsey\Uuid\Uuid;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $fakerFactory = FakerFactory::create();

        $doctor1 = new Doctor();
        $doctor1->setId(Uuid::fromString('0499b62c-a8da-4431-93c1-7ce97c2c78aa'));
        $doctor1->setIsActive(true);
        $doctor1->setIsPremium(true);
        $doctor1->setRegisteredAt(new \DateTime('2019-01-01 12:00:00'));
        $doctor1->setEmail($fakerFactory->email);
        $manager->persist($doctor1);

		$doctor2 = new Doctor();
		$doctor2->setId(Uuid::fromString('1c603cfa-8049-47c1-8242-6d51d457216a'));
		$doctor2->setIsActive(true);
		$doctor2->setIsPremium(false);
		$doctor2->setRegisteredAt(new \DateTime('2019-01-01 13:00:00'));
        $doctor2->setEmail($fakerFactory->email);
		$manager->persist($doctor2);

		$doctor3 = new Doctor();
		$doctor3->setId(Uuid::fromString('12039062-1bc1-4cca-9772-a1b1cb12cea8'));
		$doctor3->setIsActive(false);
		$doctor3->setIsPremium(false);
		$doctor3->setRegisteredAt(new \DateTime('2019-01-01 14:00:00'));
        $doctor3->setEmail($fakerFactory->email);
		$manager->persist($doctor3);

		$booking = new Booking();
		$booking->setDoctor($doctor1);
		$booking->setDate(new \DateTime('2019-01-02 12:00:00'));
		$booking->setPatient('Jan Kowalski');

        $manager->flush();
    }
}
