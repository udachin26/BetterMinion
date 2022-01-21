<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\economy;

use cooldogedev\BedrockEconomy\BedrockEconomy;
use pocketmine\player\Player;
use pocketmine\Server;

class BedrockEconomyProvider implements EconomyProvider{

	public function get(Player $player) : ?float{
		return $this->getEconomy()->getAPI()->getPlayerBalance($player->getName());
	}

	public function add(Player $player, float $amount = 0) : void{
		$this->getEconomy()->getAPI()->addToPlayerBalance($player->getName(), (int) $amount);
	}

	public function reduce(Player $player, float $amount = 0) : void{
		$this->getEconomy()->getAPI()->subtractFromPlayerBalance($player->getName(), (int) $amount);
	}

	public function set(Player $player, float $amount = 0) : void{
		$this->getEconomy()->getAPI()->setPlayerBalance($player->getName(), (int) $amount);
	}

	public function getEconomy() : mixed{
		return Server::getInstance()->getPluginManager()->getPlugin("BedrockEconomy");
	}

}