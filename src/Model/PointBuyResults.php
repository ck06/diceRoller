<?php

declare(strict_types=1);

namespace App\Model;

use InvalidArgumentException;

class PointBuyResults
{
    private int $pointsSpent = 0;

    public function __construct(
        private PointBuyConfig $config,
        private int $str = 8,
        private int $dex = 8,
        private int $con = 8,
        private int $int = 8,
        private int $wis = 8,
        private int $cha = 8
    ) {
        $this->recalculatePointsSpent();
    }

    public function getConfig(): PointBuyConfig
    {
        return $this->config;
    }

    public function setConfig(PointBuyConfig $config): self
    {
        $this->config = $config;
        $this->recalculatePointsSpent();

        return $this;
    }

    public function getStr(): int
    {
        return $this->str;
    }

    public function setStr(int $str): self
    {
        $this->str = $str;
        $this->recalculatePointsSpent();

        return $this;
    }

    public function getDex(): int
    {
        return $this->dex;
    }

    public function setDex(int $dex): self
    {
        $this->dex = $dex;
        $this->recalculatePointsSpent();

        return $this;
    }

    public function getCon(): int
    {
        return $this->con;
    }

    public function setCon(int $con): self
    {
        $this->con = $con;
        $this->recalculatePointsSpent();

        return $this;
    }

    public function getInt(): int
    {
        return $this->int;
    }

    public function setInt(int $int): self
    {
        $this->int = $int;
        $this->recalculatePointsSpent();

        return $this;
    }

    public function getWis(): int
    {
        return $this->wis;
    }

    public function setWis(int $wis): self
    {
        $this->wis = $wis;
        $this->recalculatePointsSpent();

        return $this;
    }

    public function getCha(): int
    {
        return $this->cha;
    }

    public function setCha(int $cha): self
    {
        $this->cha = $cha;
        $this->recalculatePointsSpent();

        return $this;
    }

    public function getPointsSpent(): int
    {
        return $this->pointsSpent;
    }

    public function setPointsSpent(int $pointsSpent): self
    {
        $this->pointsSpent = $pointsSpent;
        $this->recalculatePointsSpent();

        return $this;
    }

    private function recalculatePointsSpent(): void
    {
        $errors = [];
        $pointsSpent = 0;
        foreach (['str', 'dex', 'con', 'int', 'wis', 'cha'] as $attribute) {
            $score = $this->{'get' . ucfirst($attribute)}();
            if ($score > $this->config->getHighestAttributeLevel() || $score < $this->config->getLowestAttributeLevel()) {
                $errors[] = sprintf("Unable to set %s to %d", strtoupper($attribute), $score);
            }

            $pointsSpent += $this->config->getPointCostPerLevel()[$score];
        }

        if (count($errors) > 0) {
            throw new InvalidArgumentException(
                "One or more error(s) have been found: \n" .
                implode("\n", $errors)
            );
        }

        $this->pointsSpent = $pointsSpent;
    }
}
