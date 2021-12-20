<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use Mcbeany\BetterMinion\BetterMinion;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\SingletonTrait;

final class Configuration
{
    use SingletonTrait;

    public static function load()
    {
        BetterMinion::getInstance()->saveDefaultConfig();
        BetterMinion::getInstance()->getConfig()->setDefaults(self::default());
        self::setInstance(new self());
    }

    public function language(): string
    {
        return BetterMinion::getInstance()->getConfig()->get("language");
    }

    public function minion_item(): Item
    {
        $itemName = (string) BetterMinion::getInstance()->getConfig()->get("minion-item");
        return StringToItemParser::getInstance()->parse($itemName)
            ?? LegacyStringToItemParser::getInstance()->parse($itemName);
    }

    private static function default(): array
    {
        return [
            "language" => Language::FALLBACK_LANGUAGE,
            "minion-item" => "nether_star"
        ];
    }

}