<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\StringParserConstants;
use App\Interface\ParserInterface;
use App\Model\DiceRollConfig;

class DiceStringParser implements ParserInterface
{
    public function supports($input): bool
    {
        if (!is_string($input)) {
            return false;
        }

        // verify that string includes at least one die roll pattern (i.e. "1d20")
        $pattern = sprintf('/^(%s)/', implode('|', StringParserConstants::getPatterns()));
        if (!preg_match($pattern, $input)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $input
     *
     * @return array<DiceRollConfig>
     */
    public function parse($input): array
    {
        $diceStrings = $this->parseInput($input);
        $configs = [];
        foreach ($diceStrings as $diceString) {
            $configs[] = $this->parseDiceString($diceString);
        }

        return $configs;
    }

    private function parseInput(string $input): array
    {
        $diceStrings = [];
        preg_match_all(StringParserConstants::getRegex(), $input, $diceStrings);

        return $diceStrings[0];
    }

    private function parseDiceString(string $diceString): DiceRollConfig
    {
        $config = new DiceRollConfig();
        $config->setOriginalString($diceString);

        $config->setMode('+');
        if (str_starts_with($diceString, '-') || str_starts_with($diceString, '+')) {
            $config->setMode($diceString[0]);
            $diceString = substr($diceString, 1);
        }

        $rollInstruction = [];
        preg_match('/^\d+(?:d\d+)?/', $diceString, $rollInstruction);
        $rollInstruction = $rollInstruction[0];

        $config->setRoll($rollInstruction);
        $diceString = substr($diceString, strlen($rollInstruction));

        $modifiers = [];
        preg_match_all('/\w{2}\d?/', $diceString, $modifiers);
        $modifiers = $modifiers[0];
        $config->setModifiers($modifiers);

        return $config;
    }
}
