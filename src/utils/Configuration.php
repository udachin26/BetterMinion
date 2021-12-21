<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use Mcbeany\BetterMinion\BetterMinion;
use pocketmine\item\Item;
use pocketmine\lang\Language;

final class Configuration{

	public static function load(){
		BetterMinion::getInstance()->saveDefaultConfig();
		BetterMinion::getInstance()->getConfig()->setDefaults(self::default());
	}

	private static function default() : array{
		return [
			"language" => Language::FALLBACK_LANGUAGE,
			"minion-item" => "nether_star"
		];
	}

	public static function language() : string{
		return BetterMinion::getInstance()->getConfig()->get("language");
	}

	public static function minion_spawner() : Item{
		$itemName = (string) BetterMinion::getInstance()->getConfig()->get("minion-spawner");
		return Utils::parseItem($itemName);
	}

}