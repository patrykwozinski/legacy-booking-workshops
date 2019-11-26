<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use ApiTestCase\JsonApiTestCase;
use App\Entity\Doctor;
use Symfony\Component\HttpFoundation\Response;

final class GetBookingsControllerTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function gets_existing_bookings_for_doctor(): void
    {
        $fixtures = $this->loadFixturesFromFile('bookings.yml');
        /** @var Doctor $doctor */
        $doctor = $fixtures['doctor'];

        $this->client->request('GET', $this->getBookingsUrl($doctor->getId()->toString()));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'booking/get_bookings_response', Response::HTTP_OK);
    }

    private function getBookingsUrl(string $doctorId): string
    {
        return sprintf('/bookings?doctor_id=%s', $doctorId);
    }
}
