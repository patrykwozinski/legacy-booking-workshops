<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use ApiTestCase\JsonApiTestCase;
use App\SDK\AvailabilityApiClient\AvailabilityApiClientInterface;
use App\SDK\AvailabilityApiClient\IO\Doctor as SdkDoctor;
use App\Tests\ObjectMother\AvailabilityClient\AvailabilityMother;
use DateTime;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;

final class BookControllerTest extends JsonApiTestCase
{
    private const DOCTOR_ID = '1d67f650-76ab-42dc-a18a-d42b5a85a454';

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
    public function cannot_book_when_date_time_is_reserved(): void
    {
        $this->doctorsDateTimeIsReserved();

        $this->client->request('POST', $this->getBookDoctorPatientUrl(self::DOCTOR_ID, $this->tomorrowDate));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'availability/not_available_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function cannot_book_when_date_time_not_exists_in_calendar(): void
    {
        $this->doctorsDateTimeMissingInCalendar();

        $this->client->request('POST', $this->getBookDoctorPatientUrl(self::DOCTOR_ID, $this->tomorrowDate));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'availability/not_available_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function cannot_book_when_date_from_past(): void
    {
        $this->doctorsDateTimeAvailable();

        $this->client->request('POST', $this->getBookDoctorPatientUrl(self::DOCTOR_ID, '2010-01-01 10:00'));

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'booking/cannot_when_date_from_past_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function booked_successfully(): void
    {
        $this->availability->reserve(
            new SdkDoctor(self::DOCTOR_ID),
            new \DateTime($this->tomorrowDate)
        );
        $this->doctorsDateTimeAvailable();

        $this->client->request('POST', $this->getBookDoctorPatientUrl(self::DOCTOR_ID, $this->tomorrowDate));

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
            Argument::type(\DateTime::class)
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
            Argument::type(\DateTime::class)
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
            Argument::type(\DateTime::class)
        )->willReturn(
            AvailabilityMother::make()
                ->existing()
                ->notReserved()
                ->get()
        );
    }
}
