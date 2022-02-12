<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use pocketmine\item\Item;
use pocketmine\utils\Config;

class Configuration{
	use SingletonTrait;

	protected function onInit() : void{
		$this->getPlugin()->saveDefaultConfig();
		$this->getConfig()->setDefaults($this->defaults());
	}

	/**
	 * @return Item Returns default minion's spawner.
	 */
	final public function minion_spawner() : Item{
		$item = Utils::parseItem($this->get("spawner") ?? "");
		if($item === null){
			$this->setDefault("spawner");
			return $this->minion_spawner();
		}
		return $item;
	}

	public function getConfig() : Config{
		return $this->getPlugin()->getConfig();
	}

	public function get(string $key) : mixed{
		return $this->getConfig()->get($key, null);
	}

	/**
	 * @return array<string, mixed> Returns default configurations.
	 */
	public function defaults() : array{
		return [
			"spawner" => "nether_star"
		];
	}

	public function setDefault(string $key) : void{
		$this->getConfig()->set($key, $this->defaults()[$key]);
		$this->getConfig()->save();
	}
}
