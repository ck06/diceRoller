<?php

declare(strict_types=1);

namespace App\Constants;

class PointBuyDialogueConstants
{
    public const TREE_POINT_BUY = 'point-buy';

    public const KEY_BACK = 98;
    public const KEY_EXIT = 99;

    public const ACTION_MODIFY_STR = 'modify-STR';
    public const ACTION_MODIFY_DEX = 'modify-DEX';
    public const ACTION_MODIFY_CON = 'modify-CON';
    public const ACTION_MODIFY_INT = 'modify-INT';
    public const ACTION_MODIFY_WIS = 'modify-WIS';
    public const ACTION_MODIFY_CHA = 'modify-CHA';

    /**
     * @return array<int,string>
     */
    public static function getDefaultAnswers(): array
    {
        return [
            static::KEY_BACK => 'back to root',
            static::KEY_EXIT => 'exit',
        ];
    }

    /**
     * @return array<string,string>
     */
    public static function getActions(): array
    {
        return [
            'STR' => static::ACTION_MODIFY_STR,
            'DEX' => static::ACTION_MODIFY_DEX,
            'CON' => static::ACTION_MODIFY_CON,
            'INT' => static::ACTION_MODIFY_INT,
            'WIS' => static::ACTION_MODIFY_WIS,
            'CHA' => static::ACTION_MODIFY_CHA,
        ];
    }
}
