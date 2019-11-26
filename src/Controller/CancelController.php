<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Doctor as DoctorEntity;
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
	public function cancelBooking(Request $request): JsonResponse
	{
		$date     = $request->get('date') ?? '';
		$doctorId = $request->get('doctor_id') ?? '';
		$patient  = $request->get('patient') ?? '';

		/** @var Registry $em */
		$em = $this->get('doctrine');
		/** @var DoctorEntity $doctor */
		$doctor = $em->getRepository(DoctorEntity::class)->find(Uuid::fromString($doctorId));
		/** @var DoctorEntity $doctor */
		$booking = $em->getRepository(Booking::class)->findOneBy([
			'doctor'  => $doctor,
			'patient' => $patient,
			'date'    => new \DateTime($date),
		]);

		if (!$doctor || !$booking)
		{
			return new JsonResponse([
				'message' => 'Booking does not exists!',
			], Response::HTTP_NOT_FOUND);
		}

		$em->getManager()->remove($booking);
		$em->getManager()->flush();

		return new JsonResponse([
			'message' => 'Booking was successfully canceled!',
		]);
	}
}
