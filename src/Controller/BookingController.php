<?php
declare(strict_types=1);

namespace App\Controller;


use App\SDK\AvailabilityApiClient\AvailabilityApiClient;
use App\SDK\AvailabilityApiClient\IO\Doctor;
use App\Service\BookingHelper;
use App\Service\BookingValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends AbstractController
{
	public function bookVisit(Request $request): Response
	{
		/** @var BookingHelper $bookingHelper */
		$bookingHelper = $this->get(BookingHelper::class);

		$date     = $request->get('date');
		$doctorId = $request->get('doctorId');

		$booking = $bookingHelper->create($date, $doctorId, $request->get('patient'));

		/** @var AvailabilityApiClient $availabilityApi */
		$availabilityApi = $this->get(AvailabilityApiClient::class);
		$availabilityApi->getAvailabilityInformation(new Doctor($doctorId), new \DateTimeImmutable($date));

		/** @var BookingValidator $validator */
		$validator     = $this->get(BookingValidator::class);
		$bookingStatus = $validator->checkIfValid($booking);

		if ($bookingStatus)
		{
			return new Response('Booked!');
		}

		return new Response('Cannot book visit with errors: ' . $bookingStatus);
	}
}
