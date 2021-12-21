<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use Mcbeany\BetterMinion\BetterMinion;
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
			BetterMinion::getInstance()->saveResource("langs".DIRECTORY_SEPARATOR."$lang.ini");
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
                "type" =>  $input
            ]
        ));
    }

    public static function target_not_found(string $input) : string{
        return self::getInstance()->translate(new Translatable(self::getInstance()->get("target.not.found"),
            [
                "target" =>  $input
            ]
        ));
    }

    public static function player_not_found(string $input) : string{
        return self::getInstance()->translate(new Translatable(self::getInstance()->get("player.not.found"),
            [
                "player" =>  $input
            ]
        ));
    }

    public static function no_selected_player() : string{
        return self::getInstance()->translate(new Translatable(self::getInstance()->get("no.selected.player")));
    }

}