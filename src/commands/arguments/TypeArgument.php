<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use Mcbeany\BetterMinion\minions\MinionType;
use pocketmine\command\CommandSender;

class TypeArgument extends StringEnumArgument{

	public function getTypeName() : string{
		return "string";
	}

	public function parse(string $argument, CommandSender $sender) : string{
		return $argument;
	}

	public function canParse(string $testString, CommandSender $sender) : bool{
		return true;
	}

	public function getEnumValues() : array{
		return array_map(fn(MinionType $type) => $type->name(), MinionType::getAll());
	}

}