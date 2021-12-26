<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use Mcbeany\BetterMinion\entities\BaseMinion;
use Mcbeany\BetterMinion\events\MinionInteractEvent;
use Mcbeany\BetterMinion\events\MinionSpawnEvent;
use Mcbeany\BetterMinion\minions\MinionInfo;
use Mcbeany\BetterMinion\minions\MinionNBT;
use Mcbeany\BetterMinion\sessions\SessionManager;
use Mcbeany\BetterMinion\utils\Configuration;
use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\nbt\tag\CompoundTag;

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
				$minionNBT = CompoundTag::create()
					->setTag(MinionNBT::INFO, $info->nbtSerialize())
					->setString(MinionNBT::OWNER, $player->getUniqueId()->toString());
				/** @var BaseMinion $entity */
				$entity = new $class(Location::fromObject(
					$world->getBlock($player->getPosition())->getPosition()->add(0.5, 0, 0.5),
					$world // TODO: Entity's yaw
				), $player->getSkin(), $minionNBT);

				$minionEvent = new MinionSpawnEvent($player, $entity);
				$minionEvent->call();
				if($minionEvent->isCancelled()){
					$event->uncancel();
					return;
				}
				$entity->spawnToAll();

				$item->pop();
				$player->getInventory()->setItemInHand($item);
			}
		}
	}

	/**
	 * @param PlayerEntityInteractEvent $event
	 *
	 * @priority HIGHEST
	 * @handleCancelled FALSE
	 */
	public function onInteractEntity(PlayerEntityInteractEvent $event) : void{
		$entity = $event->getEntity();
		$player = $event->getPlayer();
		if($entity instanceof BaseMinion){
			$event->cancel();
			$session = SessionManager::getSession($player);
			if($session->inRemoveMode()) {
				$entity->flagForDespawn();
				return;
			}
			$minionEvent = new MinionInteractEvent($player, $entity);
			$minionEvent->call();
			//TODO: Open minion GUI
		}
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority NORMAL
	 */
	public function onJoin(PlayerJoinEvent $event) : void{
		SessionManager::createSession($event->getPlayer());
	}

	/**
	 * @param PlayerQuitEvent $event
	 * @priority NORMAL
	 */
	public function onQuit(PlayerQuitEvent $event) : void{
		SessionManager::destroySession($event->getPlayer());
	}

}
