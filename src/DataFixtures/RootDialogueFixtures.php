<?php

namespace App\DataFixtures;

use App\Constants\FixtureOrderConstants;
use App\Entity\DialogueStep;
use Doctrine\Common\Collections\Criteria;

class RootDialogueFixtures extends AbstractDialogueFixtures
{
    protected function generateTree(DialogueStep $rootStep): void
    {
        $rootStep->setOutput('Pick a tree to enter: ');

        // fetch all existing trees

        $trees = $this->manager
            ->getRepository(DialogueStep::class)
            ->matching(
                (new Criteria())
                    ->where(Criteria::expr()->eq('previous', null))
                    ->andWhere(Criteria::expr()->neq('tree', $this->getDialogueTreeName()))
            );

        foreach ($trees as $tree) {
            $rootStep->addNext((new DialogueStep())
                ->setTree($this->getDialogueTreeName())
                ->setOutput($tree->getTree())
                ->setMethod('start-dialogue-' . $tree->getTree())
            );
        }
    }

    protected function getDialogueTreeName(): string
    {
        return 'root';
    }

    public function getOrder(): int
    {
        return FixtureOrderConstants::ROOT_DIALOGUE_FIXTURE;
    }
}
