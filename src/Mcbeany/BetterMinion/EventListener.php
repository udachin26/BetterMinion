<?php
declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use Mcbeany\BetterMinion\minions\MinionInformation;
use Mcbeany\BetterMinion\minions\MinionType;
use Mcbeany\BetterMinion\minions\MinionUpgrade;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
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
        $player = $event->getPlayer();
        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $mItem = Item::fromString((string) BetterMinion::getInstance()->getConfig()->get("minion-item"));
            if ($item->getId() === $mItem->getId() && $item->getDamage() === $mItem->getDamage()) {
                if (($minionInformation = $item->getNamedTag()->getCompoundTag("MinionInformation")) !== null) {
                    if (($minionType = $minionInformation->getCompoundTag("MinionType")) !== null) {
                        if (in_array($player->getLevelNonNull()->getFolderName(), BetterMinion::getInstance()->getConfig()->get("worlds", []))) return;
                        $minionUpgrade = $minionInformation->hasTag("MinionUpgrade") ? MinionUpgrade::nbtDeserialize($minionInformation->getCompoundTag("MinionUpgrade")) : new MinionUpgrade();
                        $skin = $player->getSkin();
                        $nbt = Entity::createBaseNBT($event->getBlock()->getSide($event->getFace())->add(0.5, 0, 0.5));
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
                        $event->setCancelled();
                    }
                }
            }
        }
    }
}
