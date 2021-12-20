<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use Mcbeany\BetterMinion\minions\MinionType;
use pocketmine\command\CommandSender;

class TypeArgument extends StringEnumArgument
{

    public function __construct()
    {
        parent::__construct($this->getTypeName(), true);
    }

    public function parse(string $argument, CommandSender $sender)
    {
        return MinionType::fromString($argument);
    }

    public function getTypeName(): string
    {
        return "type";
    }

    public function getEnumValues(): array
    {
        return array_map(
            fn (MinionType $type) => $type->name(),
            MinionType::getAll());
    }

    public function getEnumName(): string
    {
        return "type";
    }
}