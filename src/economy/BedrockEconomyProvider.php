<?php
declare(strict_types=1);

namespace Mcbeany\BetterMinion\economy;

use cooldogedev\BedrockEconomy\BedrockEconomy;
use pocketmine\player\Player;

class BedrockEconomyProvider implements EconomyProvider{

	public function get(Player $player) : ?float{
		return BedrockEconomy::getInstance()->getAPI()->getPlayerBalance($player->getName());
	}

	public function add(Player $player, float $amount = 0) : void{
		BedrockEconomy::getInstance()->getAPI()->addToPlayerBalance($player->getName(), (int) $amount);
	}

	public function reduce(Player $player, float $amount = 0) : void{
		BedrockEconomy::getInstance()->getAPI()->subtractFromPlayerBalance($player->getName(), (int) $amount);
	}

	public function set(Player $player, float $amount = 0) : void{
		BedrockEconomy::getInstance()->getAPI()->setPlayerBalance($player->getName(), (int) $amount);
	}
}