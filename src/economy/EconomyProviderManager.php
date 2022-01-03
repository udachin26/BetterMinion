<?php
declare(strict_types=1);

namespace Mcbeany\BetterMinion\economy;

use Mcbeany\BetterMinion\BetterMinion;
use Mcbeany\BetterMinion\utils\Configuration;

class EconomyProviderManager{

	public const BEDROCK_ECONOMY = "bedrock_economy";
	public const ECONOMY_API = "economyapi";
	public const CAPITAL = "capital";

	protected static ?EconomyProvider $provider;

	public static function load(){
		self::$provider = match (mb_strtolower(Configuration::economy_provider())) {
			self::BEDROCK_ECONOMY => new BedrockEconomyProvider(),
			self::ECONOMY_API => new EconomyAPIProvider(),
			default => null,
		};
		if (!self::$provider->checkAPI()){
			BetterMinion::getInstance()->getLogger()->error("Couldn't found selected economy plugin, disabling economy feature...");
			self::$provider = null;
		}
	}

	public static function getProvider() : ?EconomyProvider{
		return self::$provider;
	}

}