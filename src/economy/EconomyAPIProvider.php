<?php
declare(strict_types=1);

namespace Mcbeany\BetterMinion\economy;

use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;

class EconomyAPIProvider implements EconomyProvider{

	public function get(Player $player) : ?float{
		$money = EconomyAPI::getInstance()->myMoney($player);
		if(is_numeric($money)){
			return $money;
		}
		return null;
	}

	public function add(Player $player, float $amount = 0) : void{
		EconomyAPI::getInstance()->addMoney($player, $amount);
	}

	public function reduce(Player $player, float $amount = 0) : void{
		EconomyAPI::getInstance()->reduceMoney($player, $amount);
	}

	public function set(Player $player, float $amount = 0) : void{
		EconomyAPI::getInstance()->setMoney($player, $amount);
	}
}