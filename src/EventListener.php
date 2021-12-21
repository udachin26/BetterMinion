<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use Mcbeany\BetterMinion\entities\BaseMinion;
use Mcbeany\BetterMinion\events\MinionSpawnEvent;
use Mcbeany\BetterMinion\minions\MinionInfo;
use Mcbeany\BetterMinion\minions\MinionNBT;
use Mcbeany\BetterMinion\utils\Configuration;
use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

final class EventListener implements Listener{

	/**
	 * @param PlayerItemUseEvent $event
	 * @priority HIGHEST
	 * @handleCancelled FALSE
	 */
	public function onItemUse(PlayerItemUseEvent $event) : void{
		$player = $event->getPlayer();
		$world = $player->getWorld();
		$item = $event->getItem();
		if($item->equals(Configuration::minion_spawner(), true)){
			$nbt = $item->getNamedTag()->getCompoundTag(MinionNBT::INFO);
			if($nbt !== null){
				$event->cancel();
				$info = MinionInfo::nbtDeserialize($nbt);
				$class = $info->getType()->className();
				/** @var BaseMinion $entity */
				$entity = new $class(Location::fromObject(
					$world->getBlock($player->getPosition())->getPosition()->add(0.5, 0, 0.5),
					$world
				), $player->getSkin());

				$minionEvent = new MinionSpawnEvent($player, $entity);
				$minionEvent->call();
				if($minionEvent->isCancelled()){
					return;
				}
				$entity->spawnToAll();

				$item->pop();
				$player->getInventory()->setItemInHand($item);
			}
		}
	}

}
