<?php

declare(strict_types=1);

namespace App\Interface;

interface ParserInterface
{
    public function parse($input);

    public function supports($input): bool;
}
