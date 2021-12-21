<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use Mcbeany\BetterMinion\BetterMinion;
use pocketmine\item\Item;
use pocketmine\lang\Language;
use pocketmine\utils\SingletonTrait;

final class Configuration{
	use SingletonTrait;

	public static function load(){
		BetterMinion::getInstance()->saveDefaultConfig();
		BetterMinion::getInstance()->getConfig()->setDefaults(self::default());
		self::setInstance(new self());
	}

	private static function default() : array{
		return [
			"language" => Language::FALLBACK_LANGUAGE,
			"minion-item" => "nether_star"
		];
	}

	public function language() : string{
		return BetterMinion::getInstance()->getConfig()->get("language");
	}

	public function minion_spawner() : Item{
		$itemName = (string) BetterMinion::getInstance()->getConfig()->get("minion-spawner");
		return Utils::parseItem($itemName);
	}

}