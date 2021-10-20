<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use Mcbeany\BetterMinion\BetterMinion;
use Mcbeany\BetterMinion\commands\arguments\TypeArgument;
use Mcbeany\BetterMinion\minions\MinionType;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\Server;
use function is_numeric;

class GiveCommand extends BaseSubCommand
{
	protected function prepare(): void
	{
		$this->registerArgument(0, new RawStringArgument("player", true));
		$this->registerArgument(1, new TypeArgument("type", true));
		$this->registerArgument(2, new RawStringArgument("target", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
	{
		if ($sender instanceof Player && !$sender->hasPermission("betterminions.command")) {
			$sender->sendMessage("You don't have permission to use this command!");
			return;
		}
		$player = !isset($args["player"]) ? null : Server::getInstance()->getPlayer($args["player"]);
		if (!$player instanceof Player) {
			$sender->sendMessage("That player can't be found");
			return;
		}
		if (!isset($args["type"]) or !is_numeric($args["type"])) {
			$this->sendUsage();
			return;
		}
		$type = $args["type"];
		try {
			if (!isset($args["target"])) {
				$player->sendMessage("Item not found!");
				return;
			}
			$target = Item::fromString($args["target"]);
			if ($target->getId() > 255) {
				$player->sendMessage("That item can't be found");
				return;
			}
			$minionType = new MinionType($type, $target->getId(), $target->getDamage());
			$item = Item::fromString((string) BetterMinion::getInstance()->getConfig()->get("minion-item"), false);
			$item->setCustomName($minionType->getTargetName() . " Minion I");
			$item->setNamedTagEntry(new CompoundTag("MinionInformation", [
				$minionType->nbtSerialize()
			]));
			if (!$player->getInventory()->canAddItem($item)) {
				$player->sendMessage("Your inventory is full");
				return;
			}
			$player->getInventory()->addItem($item);
			$player->sendMessage("Successfully got you a minion");
		} catch (\InvalidArgumentException $exception) {
			$player->sendMessage("That item can't be found");
			return;
		}
	}
}
