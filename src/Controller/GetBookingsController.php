<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\Booking;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class GetBookingsController extends Controller
{
    /**
     * @Route( "/bookings", methods={"GET"})
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine');

        $bookings = $em->getRepository(Booking::class)->findBy([
            'doctor' => $request->get('doctor_id'),
        ]);

        $bookings = array_map(function (Booking $booking) {
            return [
                'id' => $booking->getId(),
                'date' => $booking->getDate()->format('Y-m-d H:i'),
                'patient' => $booking->getPatient(),
            ];
        }, $bookings);

        return new JsonResponse([
            'doctor_id' => $request->get('doctor_id'),
            'bookings' => $bookings,
        ]);
    }
}
