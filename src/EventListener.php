<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use Mcbeany\BetterMinion\entities\BaseMinion;
use Mcbeany\BetterMinion\events\player\PlayerInteractMinionEvent;
use Mcbeany\BetterMinion\events\player\PlayerSpawnMinionEvent;
use Mcbeany\BetterMinion\menus\inventories\MinionMainMenu;
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
use function fmod;

final class EventListener implements Listener{

	/**
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
					->setString(MinionNBT::OWNER, $player->getUniqueId()->toString())
					->setString(MinionNBT::OWNER_NAME, $player->getName());
				/** @var BaseMinion $entity */
				$entity = new $class(Location::fromObject(
					$world->getBlock($player->getPosition())->getPosition()->add(0.5, 0, 0.5),
					$world,
					fmod($player->getLocation()->getYaw(), 360)
				), $player->getSkin(), $minionNBT);

				$minionEvent = new PlayerSpawnMinionEvent($player, $entity);
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
			$minionEvent = new PlayerInteractMinionEvent($player, $entity);
			$minionEvent->call();
			(new MinionMainMenu($entity))->display($player);
		}
	}

	/**
	 * @priority NORMAL
	 */
	public function onJoin(PlayerJoinEvent $event) : void{
		SessionManager::createSession($event->getPlayer());
	}

	/**
	 * @priority NORMAL
	 */
	public function onQuit(PlayerQuitEvent $event) : void{
		SessionManager::destroySession($event->getPlayer());
	}

}
