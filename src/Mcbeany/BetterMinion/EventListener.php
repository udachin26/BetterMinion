<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use Mcbeany\BetterMinion\minions\MinionInformation;
use Mcbeany\BetterMinion\minions\MinionType;
use Mcbeany\BetterMinion\minions\MinionUpgrade;
use Mcbeany\BetterMinion\utils\MinionLimiter;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

class EventListener implements Listener
{
    /**
     * @ignoreCancelled
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $item = $event->getItem();
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $mItem = Item::fromString((string) BetterMinion::getInstance()->getConfig()->get("minion-item"), false);
            if ($item->getId() === $mItem->getId() && $item->getDamage() === $mItem->getDamage()) {
                if (($minionInformation = $item->getNamedTag()->getCompoundTag("MinionInformation")) !== null) {
                    if (($minionType = $minionInformation->getCompoundTag("MinionType")) !== null) {
                        $event->setCancelled();
                        /* if (!$block->isSolid()) {TODO: anti-glitch
                            $block = $block->getLevelNonNull()->getBlock($block->subtract(0, 1));
                        } */
                        $levelName = $player->getLevelNonNull()->getFolderName();
                        if (in_array($levelName, BetterMinion::getInstance()->getConfig()->get("worlds", []))) {
                            return;
                        }
                        if (MinionLimiter::isFull($player->getName(), $levelName)) {
                            // TODO: Message
                            return;
                        }
                        $minionUpgrade = $minionInformation->hasTag("MinionUpgrade") ? MinionUpgrade::nbtDeserialize($minionInformation->getCompoundTag("MinionUpgrade")) : new MinionUpgrade();
                        $skin = $player->getSkin();
                        $nbt = Entity::createBaseNBT($block->getSide($event->getFace())->add(0.5, 0, 0.5));
                        $nbt->setTag(new CompoundTag("Skin", [
                            new StringTag("Name", $skin->getSkinId()),
                            new ByteArrayTag("Data", $skin->getSkinData()),
                            new ByteArrayTag("CapeData", $skin->getCapeData()),
                            new StringTag("GeometryName", $skin->getGeometryName()),
                            new ByteArrayTag("GeometryData", $skin->getGeometryData())
                        ]));
                        $type = MinionType::nbtDeserialize($minionType);
                        $nbt->setTag((new MinionInformation($player->getName(), $type, $minionUpgrade, $minionInformation->getInt("Level", 1), $minionInformation->getInt("ResourcesCollected", 0)))->nbtSerialize());
                        $entityType = BetterMinion::$minions[$type->getActionType()];
                        $entity = new $entityType($player->getLevelNonNull(), $nbt);
                        $entity->spawnToAll();
                        $item->pop();
                        $player->getInventory()->setItemInHand($item);
                    }
                }
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if (isset(BetterMinion::getInstance()->isRemove[$player->getName()])) {
            unset(BetterMinion::getInstance()->isRemove[$player->getName()]);
        }
    }
}
