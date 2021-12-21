<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;

final class Utils
{

    /**
     * @throws LegacyStringToItemParserException
     */
    public static function parseItem(string $item) : Item{
        return StringToItemParser::getInstance()->parse($item)
        ?? LegacyStringToItemParser::getInstance()->parse($item);
    }

}