<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class BookingControllerTest extends JsonApiTestCase
{
    private const MISSING_DOCTOR_ID = '673519ef-07c1-446e-ac2e-ce14d7a2dcc4';

    /**
     * @test
     */
    public function returns_not_found_response_when_book_for_doctor_which_does_not_exist(): void
    {
        $this->client->request('POST', $this->getBookDoctorPatientUrl(self::MISSING_DOCTOR_ID));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_I_AM_A_TEAPOT);
    }

    private function getBookDoctorPatientUrl(string $doctorId): string
    {
        return sprintf('/book?doctor_id=%s', $doctorId);
    }
}
