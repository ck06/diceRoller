<?php

namespace App\DataFixtures;

use App\Constants\FixtureOrderConstants;
use App\Constants\PointBuyDialogueConstants;
use App\Entity\DialogueStep;

class PointBuyDialogueFixtures extends AbstractDialogueFixtures
{
    protected function generateTree(DialogueStep $rootStep): void
    {
        $rootStep->setOutput('Which attribute do you want to modify?');
        $this->generateBuyTree($rootStep);
        $this->generateConfigTree($rootStep);
    }

    private function generateBuyTree(DialogueStep $rootStep): void
    {
        foreach (['STR', 'DEX', 'CON', 'INT', 'WIS', 'CHA'] as $attribute) {
            $nextStep = (new DialogueStep())
                ->setOutput($attribute)
                ->addNext(
                    (new DialogueStep())
                        ->setOutput('Please enter the new value.')
                        ->setMethod(PointBuyDialogueConstants::getActions()[$attribute])
                );

            $rootStep->addNext($nextStep);
        }
    }

    private function generateConfigTree(DialogueStep $rootStep): void
    {
        // TODO
    }

    protected function getDialogueTreeName(): string
    {
        return 'point-buy';
    }

    public function getOrder(): int
    {
        // all dialogue fixtures should run at the same time
        return FixtureOrderConstants::POINT_BUY_DIALOGUE_FIXTURES;
    }
}
