<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\Booking;
use App\Entity\Doctor as DoctorEntity;
use App\SDK\AvailabilityApiClient\AvailabilityApiClient;
use App\SDK\AvailabilityApiClient\IO\Doctor as SdkDoctor;
use App\Service\BookingHelper;
use App\Service\BookingValidator;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BookingController extends Controller
{
	public function bookVisit(Request $request): JsonResponse
	{
		$date = $request->get('date') ?? '';
		$doctorId = $request->get('doctorId') ?? '';
		$patient = $request->get('patient') ?? '';

		/** @var Registry $em */
		$em = $this->get('doctrine');
		/** @var DoctorEntity $doctor */
		$doctor = $em->getRepository(DoctorEntity::class)->find(Uuid::fromString($doctorId));

		if (!$doctor) {
			throw new HttpException(418);
		}

		/** @var BookingHelper $bookingHelper */
		$bookingHelper = $this->get(BookingHelper::class);
		$booking = $bookingHelper->create($date, $doctor, $request->get('patient'));

		/** @var AvailabilityApiClient $availabilityApi */
		$availabilityApi = $this->get(AvailabilityApiClient::class);
		$availability = $availabilityApi->getAvailabilityInformation(
			new SdkDoctor($doctorId),
			new \DateTimeImmutable($date)
		);

		if (false === $availability->exists() || $availability->reserved()) {
			return new JsonResponse('Given date does not exists in calendar or is reserved');
		}

		/** @var BookingValidator $validator */
		$validator = $this->get(BookingValidator::class);
		$bookingStatus = $validator->checkIfValid($booking);

		if ($bookingStatus) {
			$booking = new Booking;
			$booking->setDoctor($doctor);
			$booking->setPatient($patient);
			$booking->setDate(new \DateTime($date));
			$em->getManager()->persist($booking);
			$em->getManager()->flush();

			return new JsonResponse('Booked!');
		}

		return new JsonResponse('Cannot book visit with errors: ' . $bookingStatus);
	}

	public function getBookings(Request $request): JsonResponse
	{
		/** @var EntityManager $em */
		$em = $this->get('doctrine');

		$bookings = $em->getRepository(Booking::class)->findBy([
			'doctor' => $request->get('doctor_id'),
		]);

		$bookings = array_map(function (Booking $booking) {
			return [
				'date' => $booking->getDate()->format('Y-m-d H:i:s'),
				'patient' => $booking->getPatient(),
			];
		}, $bookings);

		return new JsonResponse([
			'doctor' => $request->get('doctor_id'),
			'bookings' => $bookings,
		]);
	}
}
