<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class GetBookingsControllerTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function gets_existing_bookings_for_doctor(): void
    {
        $this->loadFixturesFromFile('bookings.yml');

        $doctorId = 'deee497a-86ae-40a5-a840-8d9b5c57f778';

        $this->client->request('GET', $this->getBookingsUrl($doctorId));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'booking/get_bookings_response', Response::HTTP_OK);
    }

    private function getBookingsUrl(string $doctorId): string
    {
        return sprintf('/bookings?doctor_id=%s', $doctorId);
    }
}
