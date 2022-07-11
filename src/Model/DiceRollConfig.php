<?php

declare(strict_types=1);

namespace App\Model;

class DiceRollConfig
{
    private string $originalString;

    private string $mode;

    private string $roll;

    /** @var array<string> */
    private array $modifiers;

    public function getOriginalString(): string
    {
        return $this->originalString;
    }

    public function setOriginalString(string $originalString): self
    {
        $this->originalString = $originalString;

        return $this;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getRoll(): string
    {
        return $this->roll;
    }

    public function setRoll(string $instruction): self
    {
        $this->roll = $instruction;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    /**
     * @param array<string> $modifiers
     */
    public function setModifiers(array $modifiers): self
    {
        $this->modifiers = $modifiers;

        return $this;
    }
}
