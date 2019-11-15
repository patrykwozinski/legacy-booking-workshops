<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class BookingControllerTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_books_for_doctor_which_does_not_exist(): void
    {
        $this->client->request('POST', $this->getBookDoctorPatientUrl());

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_I_AM_A_TEAPOT);
    }

    private function getBookDoctorPatientUrl(): string
    {
        return '/book';
    }
}
