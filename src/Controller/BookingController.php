<?php
declare(strict_types=1);

namespace App\Controller;


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

		$booking = $bookingHelper->create(
			$request->get('date'),
			$request->get('doctorId'),
			$request->get('patient')
		);

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
