<?php
declare(strict_types=1);

namespace App\UI\Controller;


use App\Entity\Booking;
use App\SDK\AvailabilityApiClient\AvailabilityApiClientInterface;
use App\SDK\AvailabilityApiClient\IO\Doctor as SdkDoctor;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CancelController extends Controller
{
    /**
     * @Route( "/cancel-booking")
     */
    public function __invoke(Request $request): JsonResponse
    {
        $bookingId = $request->get('booking_id') ?? '';

        /** @var Registry $em */
        $em = $this->get('doctrine');
        /** @var AvailabilityApiClientInterface $availabilityApi */
        $availabilityApi = $this->get(AvailabilityApiClientInterface::class);
        /** @var Booking $booking */
        $booking = $em->getRepository(Booking::class)->findOneBy([
            'id' => $bookingId,
        ]);

        if (!$booking) {
            return new JsonResponse([
                'message' => 'Booking does not exists!',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($booking->getDate()->getTimestamp() <= time()) {
            return new JsonResponse([
                'message' => 'Unable to cancel booking from the past.',
            ], Response::HTTP_NOT_FOUND);
        }

        $em->getManager()->remove($booking);
        $em->getManager()->flush();
        $availabilityApi->cancelReservation(new SdkDoctor($booking->getDoctorId()->toString()), $booking->getDate());

        return new JsonResponse([
            'message' => 'Booking was successfully canceled!',
        ], JsonResponse::HTTP_OK);
    }
}
