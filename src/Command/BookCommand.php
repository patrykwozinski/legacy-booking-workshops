<?php
declare(strict_types=1);

namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BookCommand extends Command
{
	public function configure()
	{
		$this->setName('booking:book')
			->addOption('doctorId')
			->addOption('date')
			->addOption('patient');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$doctorId = $input->getOption('doctorId');
		$date     = $input->getOption('date');
		$patient  = $input->getOption('patient');
	}
}
