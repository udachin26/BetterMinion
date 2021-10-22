<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use Mcbeany\BetterMinion\BetterMinion;

class MinionLimiter
{
    /** @var int[][] */
    private static $playerMinions = [];

    public static function addCount(string $playerName, string $levelName): void
    {
        if (!isset(self::$playerMinions[$playerName][$levelName])) {
            self::$playerMinions[$playerName][$levelName] = 0;
        }
        ++self::$playerMinions[$playerName][$levelName];
    }

    public static function reduceCount(string $playerName, string $levelName): void
    {
        --self::$playerMinions[$playerName][$levelName];
    }

    // TODO: Better method's name
    public static function isFull(string $playerName, string $levelName): bool
    {
        if (!isset(self::$playerMinions[$playerName][$levelName])) {
            self::$playerMinions[$playerName][$levelName] = 0;
        }
        $minionCount = self::$playerMinions[$playerName][$levelName];
        $maxCount = BetterMinion::getInstance()->getConfig()->get('max-minions');
        if ($minionCount >= $maxCount) {
            return true;
        }

        return false;
    }
}
