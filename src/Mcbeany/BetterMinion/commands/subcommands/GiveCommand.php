<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use Mcbeany\BetterMinion\BetterMinion;
use Mcbeany\BetterMinion\commands\arguments\PlayerArgument;
use Mcbeany\BetterMinion\commands\arguments\TypeArgument;
use Mcbeany\BetterMinion\minions\MinionType;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\Server;

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
        /** @var Player $player */
        $player = empty($args["player"]) ? null : Server::getInstance()->getPlayer($args["player"]);
        if ($player === null) {
            return;
        }
        $type = $args["type"];
        if ($type === -1) {
            return;
        }
        try {
            $target = Item::fromString($args["target"]);
            if ($target->getId() > 255) {
                return;
            }
            $minionType = new MinionType($type, $target->getId(), $target->getDamage());
            $item = Item::fromString((string) BetterMinion::getInstance()->getConfig()->get("minion-item"));
            $item->setCustomName($minionType->getTargetName() . " Minion I");
            $item->setNamedTagEntry(new CompoundTag("MinionInformation", [
                $minionType->nbtSerialize()
            ]));
            if (!$player->getInventory()->canAddItem($item)) {
                return;
            }
            $player->getInventory()->addItem($item);
        } catch (\InvalidArgumentException $exception) {
            return;
        }
    }

}