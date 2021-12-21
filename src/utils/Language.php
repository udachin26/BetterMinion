<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use Mcbeany\BetterMinion\BetterMinion;
use pocketmine\lang\Language as PMLang;
use pocketmine\lang\Translatable;
use pocketmine\utils\SingletonTrait;

final class Language extends PMLang{
	use SingletonTrait;

	public const AVAILABLE_LANGS = [
		"eng"
	];

	public static function load(){
		foreach(self::AVAILABLE_LANGS as $lang){
			BetterMinion::getInstance()->saveResource("langs".DIRECTORY_SEPARATOR."$lang.ini");
		}
		$instance = new self(
			Configuration::getInstance()->language(),
			BetterMinion::getInstance()->getDataFolder() . "langs"
		);
		self::setInstance($instance);
	}

    public function type_not_found(string $input) : string{
        return $this->translate(new Translatable($this->get("type.not.found"),
            [
                "type" =>  $input
            ]
        ));
    }

    public function target_not_found(string $input) : string{
        return $this->translate(new Translatable($this->get("target.not.found"),
            [
                "target" =>  $input
            ]
        ));
    }

    public function player_not_found(string $input) : string{
        return $this->translate(new Translatable($this->get("player.not.found"),
            [
                "player" =>  $input
            ]
        ));
    }

    public function no_selected_player() : string{
        return $this->translate(new Translatable($this->get("no.selected.player")));
    }

}