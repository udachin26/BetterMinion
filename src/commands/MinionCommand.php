<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands;

use CortexPE\Commando\BaseCommand;
use Mcbeany\BetterMinion\commands\subcommands\GiveCommand;
use pocketmine\command\CommandSender;

class MinionCommand extends BaseCommand
{

    protected function prepare(): void
    {
        $this->setPermission("betterminion.commands");
        $this->setUsage("Usage: /minion <give|remove|ui> [options...]");
        $this->registerSubCommand(new GiveCommand($this->getOwningPlugin(), "give", "Give player a minion spawner"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage($this->getUsage());
    }

}
