<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use Mcbeany\BetterMinion\BetterMinion;
use Mcbeany\BetterMinion\minions\MinionInfo;
use pocketmine\lang\Language as PMLang;
use pocketmine\lang\Translatable;
use pocketmine\utils\SingletonTrait;

final class Language extends PMLang{
	use SingletonTrait {
		setInstance as private;
		getInstance as private;
	}

	public const AVAILABLE_LANGS = [
		"eng"
	];

	public static function load(){
		foreach(self::AVAILABLE_LANGS as $lang){
			BetterMinion::getInstance()->saveResource("langs" . DIRECTORY_SEPARATOR . "$lang.ini");
		}
		$instance = new self(
			Configuration::language(),
			BetterMinion::getInstance()->getDataFolder() . "langs"
		);
		self::setInstance($instance);
	}

	public static function type_not_found(string $input) : string{
		return self::getInstance()->translate(new Translatable(self::getInstance()->get("type.not.found"),
			[
				"type" => $input
			]
		));
	}

	public static function target_not_found(string $input) : string{
		return self::getInstance()->translate(new Translatable(self::getInstance()->get("target.not.found"),
			[
				"target" => $input
			]
		));
	}

	public static function player_not_found(string $input) : string{
		return self::getInstance()->translate(new Translatable(self::getInstance()->get("player.not.found"),
			[
				"player" => $input
			]
		));
	}

	public static function no_selected_player() : string{
		return self::getInstance()->translate(new Translatable(self::getInstance()->get("no.selected.player")));
	}

	public static function minion_spawner_name(MinionInfo $info) : string{
		return self::getInstance()->translate(new Translatable(self::getInstance()->get("minion.spawner.name"),
			[
				"type" => $info->getType()->typeName(),
				"target" => $info->getRealTarget()->getName(),
				"level" => $info->getLevel(),
				"romanLevel" => Utils::getRomanNumeral($info->getLevel())
			]
		));
	}

	public static function minion_spawner_lore(MinionInfo $info) : array{
		$contents = explode(PHP_EOL, self::getInstance()->get("minion.spawner.lore"), 4); // Max lines limit?
		$lores = [];
		foreach($contents as $content){
			$lores[] = self::getInstance()->translate(new Translatable($content,
				[
					"type" => $info->getType()->typeName(),
					"target" => $info->getRealTarget()->getName(),
					"level" => $info->getLevel(),
					"romanLevel" => Utils::getRomanNumeral($info->getLevel()),
					"autoSmelter" => var_export($info->getUpgrade()->hasAutoSmelter(), true),
					"autoSeller" => var_export($info->getUpgrade()->hasAutoSeller(), true),
					"compactor" => var_export($info->getUpgrade()->hasCompactor(), true),
					"expander" => var_export($info->getUpgrade()->hasExpander(), true)
				]
			));
		}
		return $lores;
	}

}