<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\DiceRoller;
use App\Service\DiceStringParser;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiceRollCommand extends Command
{
    public function __construct(
        private DiceStringParser $parser,
        private DiceRoller $roller
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('stats:roll')
            ->setDescription('Rolls dice to generate 6 stats. Dice rolled depend on passed string')
            ->addArgument(
                'dice-string',
                InputArgument::OPTIONAL,
                '
                    Takes the passed string and interprets it as a dice roll strategy for each stat
                    Must start with 2 numbers separated by a d, followed by extra options
                    The first number determines amount of dice rolled
                    The second number determines how many faces the dice has (2 - 20)
                    A stat cannot be lower than 1 or higher than 20. Results will be rounded if over/under this limit.
                ',
                '4d6dl1'
            )
            ->addUsage('stats:roll 1d20')
            ->addUsage('stats:roll 3d6')
            ->addUsage('stats:roll 4d6dl1')
            ->addUsage('stats:roll 4d6kh3');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->parser->supports($input->getArgument('dice-string'))) {
            throw new InvalidArgumentException('Unsupported dice string');
        }

        $diceRollConfigs = $this->parser->parse($input->getArgument('dice-string'));
        $diceRollResult = $this->roller->setConfig($diceRollConfigs)->roll();

        $message = "Your rolled stats are:" . PHP_EOL . implode(', ', $diceRollResult->toArray());
        $output->write($message, true);

        return 0;
    }
}
