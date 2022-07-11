<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DialogueStepRepository")
 */
class DialogueStep
{
    use EntityIdTrait;

    /**
     * The output text of this step of the dialogue
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private ?string $output = null;

    /**
     * The name of the dialogue tree this step belongs to.
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private ?string $tree = null;

    /**
     * The action to perform with the answer, if any.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $method = null;

    /**
     * The previous step, if any.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\DialogueStep", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private ?DialogueStep $previous = null;

    /**
     * All possible continuations for this step.
     *
     * @ORM\ManyToMany(targetEntity="DialogueStep", cascade={"persist"})
     * @var ArrayCollection<DialogueStep>
     */
    private Collection $next;

    public function __construct()
    {
        $this->next = new ArrayCollection();
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(string $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function getTree(): ?string
    {
        return $this->tree;
    }

    public function setTree(string $tree, bool $recursive = false): self
    {
        $this->tree = $tree;

        if ($recursive) {
            foreach ($this->next as $next) {
                $next->setTree($tree, $recursive);
            }
        }

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getPrevious(): ?DialogueStep
    {
        return $this->previous;
    }

    public function setPrevious(?DialogueStep $dialogueStep): self
    {
        $this->previous = $dialogueStep;

        return $this;
    }

    public function getFirst(): DialogueStep
    {
        if ($this->isRoot()) {
            return $this;
        }

        return $this->previous->getFirst();
    }

    public function isRoot(): bool
    {
        return $this->previous === null;
    }

    /**
     * @return ArrayCollection<DialogueStep>
     */
    public function getNext(): Collection
    {
        return $this->next;
    }

    public function addNext(DialogueStep $step): self
    {
        if (!$this->next->contains($step)) {
            $this->next->add($step);
        }

        $step->setPrevious($this);

        return $this;
    }

    public function removeNext(DialogueStep $step): self
    {
        if ($this->next->contains($step)) {
            $this->next->remove($step);
            $step->setPrevious(null);
        }

        return $this;
    }
}

/*
The example dialogue below has a few things to keep in mind:
 * A Step is both a question and an answer
 * if no next steps are present, the next step is returning to root.
 * if only one next step is present, automatically choose it and skip ahead.  
 * when showing answers that don't fall into the above exceptions, always include default "return" and "exit" options.

------------------------------
$output  = What do you want to do?
$options = [Spend points,Change limits]
------------------------------
$output  = Spend points
$options = [Which Attribute do you want to spend points on?]
------------------------------
$output  = Which Attribute do you want to spend points on?
$options = [STR,DEX,CON,INT,WIS,CHA]
------------------------------
$output  = STR
$options = [Please enter a new score]
------------------------------
$output  = Please enter a new score
$options = []
------------------------------
$output  = What do you want to do?
$options = [Spend points,Change limits]
 */
