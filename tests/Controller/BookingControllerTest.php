<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use ApiTestCase\JsonApiTestCase;
use App\Entity\Doctor;
use App\SDK\AvailabilityApiClient\AvailabilityApiClientInterface;
use App\SDK\AvailabilityApiClient\IO\Availability;
use App\Tests\ObjectMother\AvailabilityClient\AvailabilityMother;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;

final class BookingControllerTest extends JsonApiTestCase
{
    private const MISSING_DOCTOR_ID = '673519ef-07c1-446e-ac2e-ce14d7a2dcc4';

    /** @var AvailabilityApiClientInterface */
    private $availability;

    protected function setUp(): void
    {
        parent::setUp();

        $this->availability = $this->prophesize(AvailabilityApiClientInterface::class);

        static::$kernel->getContainer()->set(AvailabilityApiClientInterface::class, $this->availability->reveal());
    }

    /**
     * @test
     */
    public function returns_not_found_response_when_book_for_doctor_which_does_not_exist(): void
    {
        $this->client->request('POST', $this->getBookDoctorPatientUrl(self::MISSING_DOCTOR_ID));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_I_AM_A_TEAPOT);
    }

    /**
     * @test
     */
    public function cannot_book_when_date_time_is_reserved(): void
    {
        $fixture = $this->loadFixturesFromFile('doctors.yml');
        /** @var Doctor $doctor */
        $doctor = $fixture['doctor_001'];

        $this->doctorsDateTimeIsReserved();

        $this->client->request('POST', $this->getBookDoctorPatientUrl($doctor->getId()->toString()));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'availability/not_available', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function cannot_book_when_date_time_not_exists_in_calendar(): void
    {
        $fixture = $this->loadFixturesFromFile('doctors.yml');
        /** @var Doctor $doctor */
        $doctor = $fixture['doctor_001'];

        $this->doctorsDateTimeMissingInCalendar();

        $this->client->request('POST', $this->getBookDoctorPatientUrl($doctor->getId()->toString()));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'availability/not_available', Response::HTTP_OK);
    }

    private function getBookDoctorPatientUrl(string $doctorId): string
    {
        return sprintf('/book?doctor_id=%s', $doctorId);
    }

    private function doctorsDateTimeIsReserved(): void
    {
        $this->availability->getAvailabilityInformation(
            Argument::type(\App\SDK\AvailabilityApiClient\IO\Doctor::class),
            Argument::type(\DateTimeImmutable::class)
        )->willReturn(
            AvailabilityMother::make()
                ->existing()
                ->reserved()
                ->get()
        );
    }

    private function doctorsDateTimeMissingInCalendar(): void
    {
        $this->availability->getAvailabilityInformation(
            Argument::type(\App\SDK\AvailabilityApiClient\IO\Doctor::class),
            Argument::type(\DateTimeImmutable::class)
        )->willReturn(
            AvailabilityMother::make()
                ->missing()
                ->get()
        );
    }
}
