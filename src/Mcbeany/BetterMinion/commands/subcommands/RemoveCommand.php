<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

class RemoveCommand extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("player", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        /** @var Player $player */
        $player = !isset($args["player"]) ? null : Server::getInstance()->getPlayer($args["player"]);
        if ($player === null) {
            $sender->sendMessage("That player can't be found");
            return;
        }
        $sender->sendMessage("TODO");
    }
}