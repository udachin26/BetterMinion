<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use cooldogedev\BedrockEconomy\BedrockEconomy;
use pocketmine\player\Player;

class EconomyProvider{

	// Providers
	public const BEDROCK_ECONOMY = "bedrock_economy";
	public const CAPITAL = "capital";

	private static BedrockEconomy|null $provider = null;

	public static function load(){
		match (mb_strtolower(Configuration::economy_provider())) {
			self::BEDROCK_ECONOMY => self::$provider = BedrockEconomy::getInstance()
		};
	}

	public static function addMoney(Player $player){
	}

	public static function takeMoney(Player $player) : bool{
		return true; // Returns false if that amount cannot be taken from that player
	}

}
