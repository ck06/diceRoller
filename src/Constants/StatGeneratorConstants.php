<?php

declare(strict_types=1);

namespace App\Constants;

class StatGeneratorConstants
{
    public const GENERATION_TYPE_STANDARD_ARRAY = 'array';
    public const GENERATION_TYPE_DICE_STRING = 'roll';
    public const GENERATION_TYPE_POINT_BUY = 'points';

    /**
     * @return array<string>
     */
    public static function getGenerationTypes(): array
    {
        return [
            static::GENERATION_TYPE_STANDARD_ARRAY,
            static::GENERATION_TYPE_DICE_STRING,
            static::GENERATION_TYPE_POINT_BUY,
        ];
    }

    /**
     * @return array<int>
     */
    public static function getStandardArray(): array
    {
        return [15, 14, 13, 12, 10, 8];
    }

    /**
     * Returns the total point cost when buying a specific stat.
     * You cannot buy below 3 or above 18 due to +2/-2 racial modifiers.
     * If necessary, this could be manually overwritten via the front-end.
     *
     * @return array<int>
     */
    public static function getDefaultPointBuyCostsPerLevel(): array
    {
        return [
            3 => -9,
            4 => -6,
            5 => -4,
            6 => -2,
            7 => -1,
            8 => 0,
            9 => 1,
            10 => 2,
            11 => 3,
            12 => 4,
            13 => 5,
            14 => 7,
            15 => 9,
            16 => 12,
            17 => 15,
            18 => 19
        ];
    }
}
