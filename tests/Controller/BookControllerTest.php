<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use ApiTestCase\JsonApiTestCase;
use App\Entity\Doctor;
use App\SDK\AvailabilityApiClient\AvailabilityApiClientInterface;
use App\SDK\AvailabilityApiClient\IO\Doctor as SdkDoctor;
use App\Tests\ObjectMother\AvailabilityClient\AvailabilityMother;
use DateTime;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;

final class BookControllerTest extends JsonApiTestCase
{
    private const MISSING_DOCTOR_ID = '673519ef-07c1-446e-ac2e-ce14d7a2dcc4';

    /** @var AvailabilityApiClientInterface */
    private $availability;

    /** @var string */
    private $tomorrowDate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->availability = $this->prophesize(AvailabilityApiClientInterface::class);
        $this->tomorrowDate = (new DateTime())->modify('+1 day')->format('Y-m-d H:i');

        static::$kernel->getContainer()->set(AvailabilityApiClientInterface::class, $this->availability->reveal());
    }

    /**
     * @test
     */
    public function not_found_when_book_for_not_existing_doctor(): void
    {
        $this->client->request('POST', $this->getBookDoctorPatientUrl(self::MISSING_DOCTOR_ID, $this->tomorrowDate));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function cannot_book_when_date_time_is_reserved(): void
    {
        $fixture = $this->loadFixturesFromFile('doctors.yml');
        /** @var Doctor $doctor */
        $doctor = $fixture['doctor_ok'];

        $this->doctorsDateTimeIsReserved();

        $this->client->request('POST', $this->getBookDoctorPatientUrl($doctor->getId()->toString(), $this->tomorrowDate));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'availability/not_available_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function cannot_book_when_date_time_not_exists_in_calendar(): void
    {
        $fixture = $this->loadFixturesFromFile('doctors.yml');
        /** @var Doctor $doctor */
        $doctor = $fixture['doctor_ok'];

        $this->doctorsDateTimeMissingInCalendar();

        $this->client->request('POST', $this->getBookDoctorPatientUrl($doctor->getId()->toString(), $this->tomorrowDate));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'availability/not_available_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function cannot_book_when_doctor_is_not_premium(): void
    {
        $fixture = $this->loadFixturesFromFile('doctors.yml');
        /** @var Doctor $doctor */
        $doctor = $fixture['doctor_without_premium'];

        $this->doctorsDateTimeAvailable();

        $this->client->request('POST', $this->getBookDoctorPatientUrl($doctor->getId()->toString(), $this->tomorrowDate));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'booking/cannot_when_doctor_not_premium_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function cannot_book_when_doctor_is_not_active(): void
    {
        $fixture = $this->loadFixturesFromFile('doctors.yml');
        /** @var Doctor $doctor */
        $doctor = $fixture['doctor_not_active'];

        $this->doctorsDateTimeAvailable();

        $this->client->request('POST', $this->getBookDoctorPatientUrl($doctor->getId()->toString(), $this->tomorrowDate));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'booking/cannot_when_doctor_not_active_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function cannot_book_when_date_from_past(): void
    {
        $fixture = $this->loadFixturesFromFile('doctors.yml');
        /** @var Doctor $doctor */
        $doctor = $fixture['doctor_ok'];

        $this->doctorsDateTimeAvailable();

        $this->client->request('POST', $this->getBookDoctorPatientUrl($doctor->getId()->toString(), '2010-01-01 10:00'));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'booking/cannot_when_date_from_past_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function booked_successfully(): void
    {
        $fixture = $this->loadFixturesFromFile('doctors.yml');
        /** @var Doctor $doctor */
        $doctor = $fixture['doctor_ok'];

        $this->availability->reserve(
            new SdkDoctor($doctor->getId()->toString()),
            new \DateTimeImmutable($this->tomorrowDate)
        );
        $this->doctorsDateTimeAvailable();

        $this->client->request('POST', $this->getBookDoctorPatientUrl($doctor->getId()->toString(), $this->tomorrowDate));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'booking/booked_response', Response::HTTP_OK);
    }

    private function getBookDoctorPatientUrl(string $doctorId, string $date): string
    {
        return sprintf('/book?doctor_id=%s&date=%s', $doctorId, $date);
    }

    private function doctorsDateTimeIsReserved(): void
    {
        $this->availability->getAvailabilityInformation(
            Argument::type(SdkDoctor::class),
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
            Argument::type(SdkDoctor::class),
            Argument::type(\DateTimeImmutable::class)
        )->willReturn(
            AvailabilityMother::make()
                ->missing()
                ->get()
        );
    }

    private function doctorsDateTimeAvailable(): void
    {
        $this->availability->getAvailabilityInformation(
            Argument::type(SdkDoctor::class),
            Argument::type(\DateTimeImmutable::class)
        )->willReturn(
            AvailabilityMother::make()
                ->existing()
                ->notReserved()
                ->get()
        );
    }
}
