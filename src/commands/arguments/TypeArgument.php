<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use Mcbeany\BetterMinion\minions\MinionType;
use pocketmine\command\CommandSender;

class TypeArgument extends StringEnumArgument{

	public function parse(string $argument, CommandSender $sender) : string{
		return strtolower($argument);
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getEnumName() : string{
		return "string";
	}

	public function getEnumValues() : array{
		return array_values(array_map(fn (MinionType $type) => $type->name(), MinionType::getAll()));
	}

}