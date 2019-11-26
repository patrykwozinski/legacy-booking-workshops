<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CancelControllerTest extends JsonApiTestCase
{
    private const MISSING_DOCTOR_ID = '673519ef-07c1-446e-ac2e-ce14d7a2dcc4';

    /**
     * @test
     */
    public function cannot_cancel_when_doctor_does_not_exist(): void
    {
        $this->client->request('POST', $this->getCancelBookingUrl(), [
            'doctor_id' => self::MISSING_DOCTOR_ID,
        ]);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    private function getCancelBookingUrl(): string
    {
        return sprintf('/cancel-booking');
    }
}
