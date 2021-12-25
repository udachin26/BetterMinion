<?php
declare(strict_types=1);

namespace Mcbeany\BetterMinion\economy;

use Mcbeany\BetterMinion\utils\Configuration;

class EconomyProviderManager{

	public const BEDROCK_ECONOMY = "bedrock_economy";
	public const ECONOMY_API = "economyapi";
	public const CAPITAL = "capital";

	protected ?EconomyProvider $provider = null;

	public function __construct(){
		switch(mb_strtolower(Configuration::economy_provider())) {
			case self::BEDROCK_ECONOMY:
				$this->provider = new BedrockEconomyProvider();
				break;
			case self::ECONOMY_API:
				$this->provider = new EconomyAPIProvider();
				break;
		}
	}

	public function getEconomyProvider() : EconomyProvider{
		return $this->provider;
	}
}