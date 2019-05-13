<?php
declare(strict_types=1);

namespace App\Command;


use App\SDK\AvailabilityApiClient\AvailabilityApiClient;
use App\SDK\AvailabilityApiClient\IO\Doctor;
use App\Service\BookingHelper;
use App\Service\BookingValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BookCommand extends Command
{
	private $bookingHelper;
	private $availabilityApiClient;
	private $bookingValidator;

	public function __construct(
		BookingHelper $bookingHelper,
		AvailabilityApiClient $availabilityApiClient,
		BookingValidator $bookingValidator
	) {
		parent::__construct();

		$this->bookingHelper         = $bookingHelper;
		$this->availabilityApiClient = $availabilityApiClient;
		$this->bookingValidator      = $bookingValidator;
	}

	public function configure()
	{
		$this->setName('booking:book')
			->addOption('doctorId')
			->addOption('date')
			->addOption('patient');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$doctorId = (int)$input->getOption('doctorId');
		$date     = $input->getOption('date');
		$patient  = $input->getOption('patient');

		$booking      = $this->bookingHelper->create($date, $doctorId, $patient);
		$availability = $this->availabilityApiClient->getAvailabilityInformation(
			new Doctor($doctorId),
			new \DateTimeImmutable($date)
		);

		if (false === $availability->exists() || $availability->reserved())
		{
			$output->writeln('Given date does not exists in calendar or is reserved');

			return 1;
		}

		$bookingStatus = $this->bookingValidator->checkIfValid($booking);

		if ($bookingStatus)
		{
			$output->writeln('Booked!');

			return 0;
		}

		$output->writeln('Cannot book with errors: ' . $bookingStatus);
	}
}
