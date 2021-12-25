<?php
declare(strict_types=1);

namespace Mcbeany\BetterMinion\economy;

use Mcbeany\BetterMinion\utils\Configuration;

class EconomyProviderManager{

	public const BEDROCK_ECONOMY = "bedrock_economy";
	public const ECONOMY_API = "economyapi";
	public const CAPITAL = "capital";

	protected ?EconomyProvider $provider;

	public function __construct(){
		$this->provider = match (mb_strtolower(Configuration::economy_provider())) {
			self::BEDROCK_ECONOMY => new BedrockEconomyProvider(),
			self::ECONOMY_API => new EconomyAPIProvider(),
			default => null,
		};
	}

	public function getEconomyProvider() : ?EconomyProvider{
		return $this->provider;
	}
}