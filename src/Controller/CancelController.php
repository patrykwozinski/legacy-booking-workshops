<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Doctor as DoctorEntity;
use App\SDK\AvailabilityApiClient\AvailabilityApiClientInterface;
use App\SDK\AvailabilityApiClient\IO\Doctor as SdkDoctor;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ramsey\Uuid\Uuid;
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
		$date     = new \DateTime($request->get('date') ?? 'now');
		$doctorId = $request->get('doctor_id') ?? '';
		$patient  = $request->get('patient') ?? '';

		/** @var Registry $em */
		$em = $this->get('doctrine');
		/** @var AvailabilityApiClientInterface $availabilityApi */
		$availabilityApi = $this->get(AvailabilityApiClientInterface::class);
		/** @var DoctorEntity $doctor */
		$doctor = $em->getRepository(DoctorEntity::class)->find(Uuid::fromString($doctorId));
		/** @var DoctorEntity $doctor */
		$booking = $em->getRepository(Booking::class)->findOneBy([
			'doctor'  => $doctor,
			'patient' => $patient,
			'date'    => $date,
		]);

		if ($date->getTimestamp() <= time()) {
			return new JsonResponse([
				'message' => 'Unable to cancel booking from the past.',
			], Response::HTTP_NOT_FOUND);
		}

		if (!$doctor || !$booking)
		{
			return new JsonResponse([
				'message' => 'Booking does not exists!',
			], Response::HTTP_NOT_FOUND);
		}

		$em->getManager()->remove($booking);
		$em->getManager()->flush();
		$availabilityApi->cancelReservation(new SdkDoctor($doctorId), new DateTimeImmutable($date));

		return new JsonResponse([
			'message' => 'Booking was successfully canceled!',
		]);
	}
}
