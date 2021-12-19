<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

class MinionCommand extends BaseCommand
{
    protected function prepare(): void
    {
        $this->setPermission("betterminion.commands");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
    }

}
