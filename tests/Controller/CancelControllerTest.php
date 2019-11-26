<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use ApiTestCase\JsonApiTestCase;
use App\Entity\Booking;
use Symfony\Component\HttpFoundation\Response;

final class CancelControllerTest extends JsonApiTestCase
{
    private const MISSING_BOOKING_ID = '673519ef-07c1-446e-ac2e-ce14d7a2dcc4';

    /**
     * @test
     */
    public function cannot_cancel_when_doctor_does_not_exist(): void
    {
        $this->client->request('POST', $this->getCancelBookingUrl(self::MISSING_BOOKING_ID));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function cannot_cancel_booking_from_the_past(): void
    {
        $fixtures = $this->loadFixturesFromFile('bookings.yml');
        /** @var Booking $booking */
        $booking = $fixtures['past_booking'];

        $this->client->request('POST', $this->getCancelBookingUrl($booking->getId()->toString()));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    private function getCancelBookingUrl(string $bookingId): string
    {
        return sprintf('/cancel-booking?booking_id=%s', $bookingId);
    }
}
