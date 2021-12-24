<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use Mcbeany\BetterMinion\entities\BaseMinion;
use Mcbeany\BetterMinion\events\MinionInteractEvent;
use Mcbeany\BetterMinion\events\MinionSpawnEvent;
use Mcbeany\BetterMinion\minions\MinionInfo;
use Mcbeany\BetterMinion\minions\MinionNBT;
use Mcbeany\BetterMinion\utils\Configuration;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;

final class EventListener implements Listener{

	/**
	 * @param PlayerItemUseEvent $event
	 *
	 * @priority HIGHEST
	 * @handleCancelled FALSE
	 */
	public function onItemUse(PlayerItemUseEvent $event) : void{
		$player = $event->getPlayer();
		$world = $player->getWorld();
		$item = $event->getItem();
		if($item->equals(Configuration::minion_spawner(), true, false)){
			$nbt = $item->getNamedTag()->getCompoundTag(MinionNBT::INFO);
			if($nbt !== null){
				$event->cancel();
				$info = MinionInfo::nbtDeserialize($nbt);
				$class = $info->getType()->className();
				/** @var BaseMinion $entity */
				$entity = new $class(Location::fromObject(
					$world->getBlock($player->getPosition())->getPosition()->add(0.5, 0, 0.5),
					$world
				// TODO: Entity's yaw
				), $player->getSkin(), $info->nbtSerialize()->setString(MinionNBT::OWNER, $player->getUniqueId()->toString()));

				$minionEvent = new MinionSpawnEvent($player, $entity);
				$minionEvent->call();
				if($minionEvent->isCancelled()){
					$event->uncancel();
					return;
				}
				$entity->setScale(0.5);
				$entity->spawnToAll();

				$item->pop();
				$player->getInventory()->setItemInHand($item);
			}
		}
	}

	/**
	 * @param EntityDamageByEntityEvent $event
	 *
	 * @priority HIGHEST
	 * @handleCancelled FALSE
	 */
	public function onEntityDamageByEntity(EntityDamageByEntityEvent $event){
		$entity = $event->getEntity();
		$damager = $event->getDamager();
		if($entity instanceof BaseMinion){
			//$event->cancel();
			if($damager instanceof Player){
				$minionEvent = new MinionInteractEvent($damager, $entity);
				$minionEvent->call();
				if($minionEvent->isCancelled()){
					return;
				}
			}
		}
	}

}
