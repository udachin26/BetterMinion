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
        $this->registerArgument(0, new RawStringArgument("player", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $player = null;
        if ($sender instanceof Player) {
            $player = $sender;
        }
        if (isset($args["player"])) {
            $player = Server::getInstance()->getPlayer($args["player"]);
        }
        if ($player === null) {
            return;
        }
        // TODO
    }

}