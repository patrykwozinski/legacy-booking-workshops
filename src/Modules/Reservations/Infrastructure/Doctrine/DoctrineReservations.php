<?php
declare(strict_types=1);

namespace App\Modules\Reservations\Infrastructure\Doctrine;


use App\Modules\Reservations\Domain\Reservation;
use App\Modules\Reservations\Domain\ReservationId;
use App\Modules\Reservations\Domain\Reservations;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineReservations implements Reservations
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Reservation $reservation): void
    {
        //
    }

    public function remove(Reservation $reservation): void
    {
        // TODO: Implement remove() method.
    }

    public function find(ReservationId $reservationId): Reservation
    {
        // TODO: Implement find() method.
    }
}
