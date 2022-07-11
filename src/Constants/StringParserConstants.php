<?php

declare(strict_types=1);

namespace App\Constants;

class StringParserConstants
{
    public const MODIFIER_KEEP_HIGHEST = 'kh\d+?';
    public const MODIFIER_DROP_HIGHEST = 'dh\d+?';
    public const MODIFIER_KEEP_LOWEST = 'kl\d+?';
    public const MODIFIER_DROP_LOWEST = 'dl\d+?';

    public const PATTERN_DICE_ROLL = '\d+d\d+';
    public const PATTERN_STATIC_NUMBER = '\d+';

    public const PATTERN_SEPARATOR_SUB = '\-';
    public const PATTERN_SEPARATOR_ADD = '\+';

    /**
     * @return array<string,string>
     */
    public static function getPatterns(): array
    {
        return [
            static::PATTERN_DICE_ROLL,

            // static number must be last as a fallback in case the previous don't match
            static::PATTERN_STATIC_NUMBER,
        ];
    }

    /**
     * @return array<string,string>
     */
    public static function getModifiers(): array
    {
        return [
            static::MODIFIER_KEEP_HIGHEST,
            static::MODIFIER_DROP_HIGHEST,
            static::MODIFIER_KEEP_LOWEST,
            static::MODIFIER_DROP_LOWEST,
        ];
    }

    /**
     * @return array<string,string>
     */
    public static function getSeparators(): array
    {
        return [
            static::PATTERN_SEPARATOR_ADD,
            static::PATTERN_SEPARATOR_SUB,
        ];
    }

    public static function getRegex(): string
    {
        // for reference / debugging, the old hardcoded string was the following;
        // '/[+\-]?\d+(?:d\d+)?(?:[kd][hl]\d+){0,2}+/i'

        $separators = sprintf('[%s]', implode('', static::getSeparators()));
        $modifiers = implode('|', static::getModifiers());
        $patterns = implode('|', static::getPatterns());

        return sprintf('/%s?(?:%s)(?:%s){0,2}/i', $separators, $patterns, $modifiers);
    }
}
