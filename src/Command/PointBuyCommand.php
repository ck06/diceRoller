<?php

declare(strict_types=1);

namespace App\Command;

use App\Constants\PointBuyDialogueConstants;
use App\Entity\DialogueStep;
use App\Model\PointBuyConfig;
use App\Model\PointBuyResults;
use App\Repository\DialogueStepRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class PointBuyCommand extends Command
{
    private QuestionHelper $helper;
    private DialogueStepRepository $dialogueStepRepository;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->helper = new QuestionHelper();
        $this->dialogueStepRepository = $em->getRepository(DialogueStep::class);
    }

    protected function configure()
    {
        $this
            ->setName('stats:buy')
            ->setDescription('Start the interactive point buy dialogue');
    }

    /**
     * @throws NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $attributes = new PointBuyResults(new PointBuyConfig());
        $dialogue = $this->dialogueStepRepository->getRoot(PointBuyDialogueConstants::TREE_POINT_BUY);
        $defaults = PointBuyDialogueConstants::getDefaultAnswers();
        while (true) {
            if ($dialogue->getNext()->count() === 1) {
                // for this dialogue in particular, it's assumed that only 1 step in $next
                // means that this is an answer selected by the user and should be skipped
                $dialogue = $dialogue->getNext()->first();
                continue;
            }

            // array_merge does not preserve the keys because they are numeric, so we have to add them like this.
            $answers = array_map(fn(DialogueStep $next) => $next->getOutput(), $dialogue->getNext()->toArray());
            foreach ($defaults as $key => $value) {
                $answers[$key] = $value;
            }

            $question = $dialogue->getMethod() !== null
                ? new Question($dialogue->getOutput() . PHP_EOL)
                : new ChoiceQuestion($dialogue->getOutput(), $answers);

            // this message should clear the console.
            $output->write("\033\143");
            $this->outputAttributeDetailsMessage($output, $attributes);
            $answer = $this->helper->ask($input, $output, $question);

            if ($answer === $defaults[PointBuyDialogueConstants::KEY_EXIT]) {
                break;
            }

            if ($answer === $defaults[PointBuyDialogueConstants::KEY_BACK]) {
                $dialogue = $dialogue->getFirst();
                continue;
            }

            foreach ($dialogue->getNext() as $next) {
                if ($next->getOutput() === $answer) {
                    $dialogue = $next;
                    continue 2;
                }
            }

            // if we reach this point, assume we have a method and go back to root afterwards
            $this->handleAnswer($answer, $dialogue->getMethod(), $attributes);
            $dialogue = $dialogue->getFirst();
        }

        return 0;
    }

    private function handleAnswer(string $answer, string $method, PointBuyResults $attributes): void
    {
        switch ($method) {
            case PointBuyDialogueConstants::ACTION_MODIFY_STR:
                $attributes->setStr((int)$answer);
                return;
            case PointBuyDialogueConstants::ACTION_MODIFY_DEX:
                $attributes->setDex((int)$answer);
                return;
            case PointBuyDialogueConstants::ACTION_MODIFY_CON:
                $attributes->setCon((int)$answer);
                return;
            case PointBuyDialogueConstants::ACTION_MODIFY_INT:
                $attributes->setInt((int)$answer);
                return;
            case PointBuyDialogueConstants::ACTION_MODIFY_WIS:
                $attributes->setWis((int)$answer);
                return;
            case PointBuyDialogueConstants::ACTION_MODIFY_CHA:
                $attributes->setCha((int)$answer);
                return;
        }
    }

    private function outputAttributeDetailsMessage(
        OutputInterface $output,
        PointBuyResults $attributes
    ): void {
        $config = $attributes->getConfig();
        $format = <<<FORMAT
Points: %d/%d
STR: %s%d (increase cost: %d, decrease gain: %d)
DEX: %s%d (increase cost: %d, decrease gain: %d)
CON: %s%d (increase cost: %d, decrease gain: %d)
INT: %s%d (increase cost: %d, decrease gain: %d)
WIS: %s%d (increase cost: %d, decrease gain: %d)
CHA: %s%d (increase cost: %d, decrease gain: %d)
FORMAT;

        $space = fn(int $input) => $input >= 10 ? '' : ' ';
        $costs = $config->getPointCostPerLevel();
        $str = $attributes->getStr();
        $dex = $attributes->getDex();
        $con = $attributes->getCon();
        $int = $attributes->getInt();
        $wis = $attributes->getWis();
        $cha = $attributes->getCha();

        $output->writeln(sprintf($format . PHP_EOL,
            $config->getPointsToSpend() - $attributes->getPointsSpent(), $config->getPointsToSpend(),
            $space($str), $str, ($costs[$str + 1] ?? 0) - $costs[$str], $costs[$str] - ($costs[$str - 1] ?? 0),
            $space($dex), $dex, ($costs[$dex + 1] ?? 0) - $costs[$dex], $costs[$dex] - ($costs[$dex - 1] ?? 0),
            $space($con), $con, ($costs[$con + 1] ?? 0) - $costs[$con], $costs[$con] - ($costs[$con - 1] ?? 0),
            $space($int), $int, ($costs[$int + 1] ?? 0) - $costs[$int], $costs[$int] - ($costs[$int - 1] ?? 0),
            $space($wis), $wis, ($costs[$wis + 1] ?? 0) - $costs[$wis], $costs[$wis] - ($costs[$wis - 1] ?? 0),
            $space($cha), $cha, ($costs[$cha + 1] ?? 0) - $costs[$cha], $costs[$cha] - ($costs[$cha - 1] ?? 0),
        ));
    }
}
