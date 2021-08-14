<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands;

use CortexPE\Commando\BaseCommand;
use Mcbeany\BetterMinion\commands\subcommands\GiveCommand;
use Mcbeany\BetterMinion\commands\subcommands\RemoveCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class MinionCommand extends BaseCommand
{

    protected function prepare(): void
    {
        $this->registerSubCommand(new GiveCommand("give", "Give you a minion spawner"));
        $this->registerSubCommand(new RemoveCommand("remove", "Quickly remove minions"));
        $this->setPermission("betterminion.commands");
        $this->setUsage("/minion give|remove");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player && !$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command!");
            return;
        }
        $this->sendUsage();
    }

}