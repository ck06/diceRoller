<?php

declare(strict_types=1);

namespace App\Model;

class DiceRollResult
{
    public function __construct(
        private bool $success,
        private array $results = [],
    ) {
    }

    public function toArray(): array
    {
        return $this->results;
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }
}
