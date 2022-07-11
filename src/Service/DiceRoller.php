<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\DiceRollConfig;
use App\Model\DiceRollResult;
use Exception;

class DiceRoller
{
    /** @var array<DiceRollConfig> */
    private ?array $configs;

    public function roll(): DiceRollResult
    {
        if (!$this->configs || count($this->configs) === 0) {
            return new DiceRollResult(false, ['Error' => 'No config set to roll dice with']);
        }

        // hardcoded # of stats; STR/DEX/CON/INT/WIS/CHA
        $results = [];
        for ($stats = 6, $i = 0; $i < $stats; $i++) {
            $rolledStat = $this->rollStat();
            if ($rolledStat === null) {
                return new DiceRollResult(false, ['Error' => 'Unresolvable modifier in string']);
            }

            $results[$i] = $this->rollStat();
        }

        rsort($results, SORT_NUMERIC);
        return new DiceRollResult(true, $results);
    }

    private function rollStat(): ?int
    {
        $statTotal = 0;
        foreach ($this->configs as $config) {
            if (!str_contains($config->getRoll(), 'd')) {
                $statTotal = $config->getMode() === '+'
                    ? $statTotal + ($config->getRoll())
                    : $statTotal - ($config->getRoll());

                continue;
            }

            $rollTotal = 0;
            $rolls = [];
            [$dice, $faces] = explode('d', $config->getRoll());
            for ($i = 0; $i < $dice; $i++) {
                try {
                    $rolls[$i] = random_int(1, (int)$faces);
                } catch (Exception $e) {
                    $rolls[$i] = 0;
                }
            }

            rsort($rolls, SORT_NUMERIC);

            $modifiers = $config->getModifiers();
            foreach ($modifiers as $modifier) {
                if (!$this->isValidModifier($modifier)) {
                    return null;
                }

                $filteredRolls = [];
                $method = substr($modifier, 0, 1); // Drop or Keep
                $target = substr($modifier, 1, 1); // Highest or Lowest
                $amount = (int)preg_replace('/\D+/', '', $modifier) ?? 1;
                for ($i = 0; $i < $amount; $i++) {
                    // if target is highest, take first element of array. otherwise take the last element.
                    $targetRoll = $target === 'h' ? array_shift($rolls) : array_pop($rolls);

                    // if we are set to keep this value, put it onto the filtered rolls
                    if ($method === 'k') {
                        $filteredRolls[] = $targetRoll;
                    }
                }

                if (count($filteredRolls) > 0) {
                    $rolls = $filteredRolls;
                }
            }

            foreach ($rolls as $roll) {
                $rollTotal += $roll;
            }

            $statTotal += $config->getMode() === '+' ? $rollTotal : -$rollTotal;
        }

        // sanity checks
        if ($statTotal < 1) {
            return 1;
        }

        if ($statTotal > 20) {
            return 20;
        }

        return $statTotal;
    }

    private function isValidModifier(string $modifier): bool
    {
        $amount = (int)preg_replace('/\D+/', '', $modifier) ?? 1;
        if ($amount === 0) {
            return false;
        }

        // Drop or Keep
        $method = substr($modifier, 0, 1);
        if (!in_array($method, ['d', 'k'])) {
            return false;
        }

        // Highest or Lowest
        $target = substr($modifier, 1, 1);
        if (!in_array($target, ['h', 'l'])) {
            return false;
        }

        // if there's any extra letters, consider it a fail
        $thirdCharacter = substr($modifier, 2, 1);
        if (preg_match('/\d/', $thirdCharacter) === 0) {
            return false;
        }

        return true;
    }

    /**
     * @param DiceRollConfig|array<DiceRollConfig $configs
     *
     * @return $this
     */
    public function setConfig(DiceRollConfig|array $configs): self
    {
        if ($configs instanceof DiceRollConfig) {
            $configs = [$configs];
        }

        $this->configs = $configs;

        return $this;
    }
}
