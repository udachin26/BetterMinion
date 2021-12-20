<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use Mcbeany\BetterMinion\BetterMinion;
use pocketmine\lang\Language as PMLang;
use pocketmine\utils\SingletonTrait;

final class Language extends PMLang{
	use SingletonTrait;

	public const AVAILABLE_LANGS = [
		"eng"
	];

	public static function load(){
		foreach(self::AVAILABLE_LANGS as $lang){
			BetterMinion::getInstance()->saveResource("langs/$lang.ini");
		}
		$instance = new self(
			Configuration::getInstance()->language(),
			BetterMinion::getInstance()->getDataFolder() . "langs"
		);
		self::setInstance($instance);
	}
}