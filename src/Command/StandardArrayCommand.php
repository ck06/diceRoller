<?php

declare(strict_types=1);

namespace App\Command;

use App\Constants\StatGeneratorConstants;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StandardArrayCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('stats:array')
            ->setDescription('Get the standard array from the DMG');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = "Your rolled stats are:" . PHP_EOL . implode(', ', StatGeneratorConstants::getStandardArray());
        $output->write($message, true);

        return 0;
    }
}
