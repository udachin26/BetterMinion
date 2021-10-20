<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use Mcbeany\BetterMinion\BetterMinion;
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
		if ($sender instanceof Player && !$sender->hasPermission("betterminions.command")) {
			$sender->sendMessage("You don't have permission to use this command!");
			return;
		}
		/** @var Player $player */
		$player = !isset($args["player"]) ? $sender : Server::getInstance()->getPlayer($args["player"]);
		if (!$player instanceof Player) {
			$sender->sendMessage("That player can't be found");
			return;
		}
		if (isset(BetterMinion::getInstance()->isRemove[$player->getName()])) {
			unset(BetterMinion::getInstance()->isRemove[$player->getName()]);
		} else {
			BetterMinion::getInstance()->isRemove[$player->getName()] = $player->getName();
		}
		$sender->sendMessage($player->getName() . " has " . (isset(BetterMinion::getInstance()->isRemove[$sender->getName()]) ? "enter in" : "no longer in") . " removable minion mode");
	}
}
