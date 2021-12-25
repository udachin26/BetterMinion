<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands\arguments;

use CortexPE\Commando\args\BaseArgument;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class PlayerArgument extends BaseArgument{

	public function getNetworkType() : int{
		return AvailableCommandsPacket::ARG_TYPE_TARGET;
	}

	public function canParse(string $testString, CommandSender $sender) : bool{
		return true;
	}

	public function parse(string $argument, CommandSender $sender){
		return $argument;
	}

	public function getTypeName() : string{
		return "target";
	}

}