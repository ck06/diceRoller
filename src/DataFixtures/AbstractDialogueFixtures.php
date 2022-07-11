<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Constants\FixtureOrderConstants;
use App\Entity\DialogueStep;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;

abstract class AbstractDialogueFixtures extends Fixture implements OrderedFixtureInterface
{
    protected ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $rootStep = $this->getRootStep($this->getDialogueTreeName());
        $this->generateTree($rootStep);
        $this->save($rootStep);
    }

    abstract protected function getDialogueTreeName(): string;

    abstract protected function generateTree(DialogueStep $rootStep): void;

    protected function save(DialogueStep $rootStep): void
    {
        // recursively apply tree name to all subsequent nodes
        $rootStep->setTree($rootStep->getTree(), true);

        // store in fixture reference
        $this->setReference($rootStep->getTree(), $rootStep);

        // store in database
        $this->manager->persist($rootStep);
        $this->manager->flush();
    }

    protected function getRootStep(string $treeName): DialogueStep
    {
        if ($this->hasReference($treeName)) {
            $rootStep = $this->getReference($treeName);
            if (!$rootStep instanceof DialogueStep) {
                throw new RuntimeException(
                    sprintf(
                        '%s expected, got %s instead',
                        DialogueStep::class,
                        get_class($rootStep)
                    )
                );
            }

            return $rootStep;
        }

        return $this->createRootStep($treeName);
    }

    private function createRootStep(string $treeName): DialogueStep
    {
        $rootStep = new DialogueStep();
        $rootStep->setTree($treeName);

        return $rootStep;
    }
}
