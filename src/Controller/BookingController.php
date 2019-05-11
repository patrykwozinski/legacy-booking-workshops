<?php
declare(strict_types=1);

namespace App\Controller;


use App\Service\BookingHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends AbstractController
{
	public function bookVisit(Request $request): Response
	{
		/** @var BookingHelper $bookingHelper */
		$bookingHelper = $this->get(BookingHelper::class);

		if ()
		{

		}

		$booking = $bookingHelper->create($request->get('date'), $request->get('doctorId'), $request->get('patient'));

		if ($booking) {

		}

		return new Response('Booked!');
	}
}
