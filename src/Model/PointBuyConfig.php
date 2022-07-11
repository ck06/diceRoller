<?php

declare(strict_types=1);

namespace App\Model;

use App\Constants\StatGeneratorConstants;

class PointBuyConfig
{
    /** @var array<int> */
    private array $pointCostPerLevel;

    /**
     * The default values are taken from the player's handbook (chapter 1, section 3, variant rule)
     */
    public function __construct(
        private int $pointsToSpend = 27,
        private int $lowestAttributeLevel = 8,
        private int $highestAttributeLevel = 15,
    ) {
        $this->determineCosts();
    }

    public function getPointsToSpend(): int
    {
        return $this->pointsToSpend;
    }

    public function setPointsToSpend(int $pointsToSpend): self
    {
        $this->pointsToSpend = $pointsToSpend;

        return $this;
    }

    public function getLowestAttributeLevel(): int
    {
        return $this->lowestAttributeLevel;
    }

    public function setLowestAttributeLevel(int $lowestAttributeLevel): self
    {
        $this->lowestAttributeLevel = $lowestAttributeLevel;
        $this->determineCosts();

        return $this;
    }

    public function getHighestAttributeLevel(): int
    {
        return $this->highestAttributeLevel;
    }

    public function setHighestAttributeLevel(int $highestAttributeLevel): self
    {
        $this->highestAttributeLevel = $highestAttributeLevel;
        $this->determineCosts();

        return $this;
    }

    /**
     * @return array<int>
     */
    public function getPointCostPerLevel(): array
    {
        return $this->pointCostPerLevel;
    }

    private function determineCosts(): void
    {
        foreach (StatGeneratorConstants::getDefaultPointBuyCostsPerLevel() as $level => $cost) {
            if ($level < $this->lowestAttributeLevel || $level > $this->highestAttributeLevel) {
                continue;
            }

            $this->pointCostPerLevel[$level] = $cost;
        }
    }
}
