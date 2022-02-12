<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions\entities;

use Mcbeany\BetterMinion\events\minions\MinionCollectResourcesEvent;
use Mcbeany\BetterMinion\minions\informations\MinionInformation;
use Mcbeany\BetterMinion\minions\informations\MinionInventory;
use Mcbeany\BetterMinion\minions\informations\MinionNBT;
use Mcbeany\BetterMinion\utils\Configuration;
use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\entity\Human;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Base class for all minions.
 */
abstract class BaseMinion extends Human{
	public const MAX_TICKDIFF = 20;

	protected UuidInterface $owner;
	protected string $ownerName;
	protected MinionInformation $minionInformation;
	protected MinionInventory $minionInventory;

	protected int $tickWait = 0;
	protected bool $isWorking = true;

	/**
	 * Constructor of the minions, I think.
	 */
	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->owner = Uuid::uuid3(Uuid::NIL, $nbt->getString(MinionNBT::OWNER));
		$this->ownerName = $nbt->getString(MinionNBT::OWNER_NAME);
		$infoNBT = $nbt->getCompoundTag(MinionNBT::INFORMATION);
		if($infoNBT === null){
			$this->flagForDespawn();
			return;
		}
		$this->minionInformation = MinionInformation::deserializeTag($infoNBT);
		$this->minionInventory = MinionInventory::deserializeTag(
			$nbt->getListTag(MinionNBT::INVENTORY) ??
			new ListTag([], NBT::TAG_Compound)
		);
		$this->minionInventory->setSize($this->minionInformation->getLevel());
		$this->getInventory()->setItemInHand($this->getTool());
		$this->setScale(Configuration::getInstance()->minion_scale());
		$this->setNameTagAlwaysVisible(false);
	}

	/**
	 * Save the minion's information.
	 */
	public function saveNBT() : CompoundTag{
		return parent::saveNBT()
			->setString(MinionNBT::OWNER, $this->owner->toString())
			->setString(MinionNBT::OWNER_NAME, $this->ownerName)
			->setTag(MinionNBT::INFORMATION, $this->minionInformation->serializeTag())
			->setTag(MinionNBT::INVENTORY, $this->minionInventory->serializeTag());
	}

	/**
	 * Do check thing before start working.
	 */
	public function onUpdate(int $currentTick) : bool{
		if($this->isWorking()){
			$lastItem = $this->minionInventory->getItem($this->minionInventory->getSize() - 1);
			if(!$lastItem->isNull() && $lastItem->getCount() == $lastItem->getMaxStackSize()){
				$this->stopWorking();
				$this->setNameTag("My inventory is full :<");
				return true;
			}
		}else{
			$this->continueWorking();
		}
		$this->setNameTag();
		return parent::onUpdate($currentTick);
	}

	/**
	 * Minion does stuff here.
	 */
	protected function entityBaseTick(int $tickDiff = 1) : bool{
		if(!$this->isWorking()){
			return parent::entityBaseTick($tickDiff);
		}
		$this->minionAnimationTick($tickDiff);
		$this->tickWait += $tickDiff;
		$actionTime = $this->getActionTime();
		if($this->tickWait >= $actionTime){
			$times = (int) ($this->tickWait / $actionTime);
			$this->tickWait -= $actionTime * $times;
			if($this->tickWait < self::MAX_TICKDIFF){
				if($times > 1){
					$this->doOfflineAction($times - 1);
				}
				$this->onAction();
			}else{
				$this->doOfflineAction($times);
			}
		}
		return parent::entityBaseTick($tickDiff);
	}

	public function setNameTag(string $name = "") : void{
		if(empty($name)){
			$this->setNameTagVisible(false);
		}
		$this->setNameTagVisible();
		parent::setNameTag($name);
	}

	/**
	 * Returns true if the minion is working and false otherwise.
	 */
	public function isWorking() : bool{
		return $this->isWorking;
	}

	/**
	 * Force the minion to stop working.
	 */
	public function stopWorking() : void{
		$this->isWorking = false;
	}

	/**
	 * Force the minion to continue working.
	 */
	public function continueWorking() : void{
		$this->isWorking = true;
	}

	/**
	 * Target could be a block or a living entity.
	 *
	 * @return \Generator|Block[]
	 */
	public function getWorkingTargets() : \Generator{
		yield;
	}

	/**
	 * Returns the minion's action time
	 */
	public function getActionTime() : int{
		return 20; // TODO: Time based on level
	}

	/**
	 * Add stuff to the minion's inventory.
	 *
	 * @param Item[] $drops
	 */
	protected function addStuff(array $drops) : void{
		foreach($drops as $drop){
			$event = new MinionCollectResourcesEvent($this, $drop);
			$event->call();
			if($event->isCancelled()){
				continue;
			}
			$this->minionInventory->addItem($drop);
		}
	}

	/**
	 * Take stuff from the minion's inventory and add to player's inventory.
	 * Returns true if the player can add all items from the itemstack and false otherwise.
	 */
	public function takeStuff(int $slot, Player $player) : bool{
		$item = $this->minionInventory->getItem($slot);
		$addable = $player->getInventory()->getAddableItemQuantity($item);
		$player->getInventory()->addItem((clone $item)->setCount($addable));
		$this->minionInventory->setItem($slot, $item->setCount($item->getCount() - $addable));
		return $item->isNull();
	}

	/*protected function getAirBlock() : ?Block{
		foreach($this->getWorkingTargets() as $target){
			if($target instanceof Block){
				if($target->asItem()->isNull()){
					return $target;
				}
			}
		}
		return null;

	}

	protected function isContainInvalidBlock() : bool{
		foreach($this->getWorkingTargets() as $target){
			if($target instanceof Block){
				if($target->isSameType($this->minionInformation->getRealTarget()) && !$target->asItem()->isNull()){
					return true;
				}
			}
		}
		return false;
	}*/

	/**
	 * Handle minion's actions.
	 */
	protected function onAction() : void{
	}

	/**
	 * As @NgLamVN explained, onOfflineAction will be executed if there is no viewer or minion is not loaded, the thing onOfflineAction
	 * will do is just adding drops to the inventory instead of sending block breaking animation, thus can reduce server laggy.
	 * Very cool :ayyyy:
	 *
	 * @pararm int $times Number of break time
	 */
	protected function doOfflineAction(int $times) : void{
	}

	/**
	 * Do minion animation
	 */
	protected function minionAnimationTick(int $tickDiff = 1) : void{
	}

	/**
	 * Returns the owner of the minion.
	 */
	public function getOwner() : UuidInterface{
		return $this->owner;
	}

	/**
	 * Returns the owner's name of the minion.
	 */
	public function getOwnerName() : string{
		return $this->ownerName;
	}

	/**
	 * Returns original name of the minion, include its owner.
	 */
	public function getOriginalNameTag() : string{
		return $this->ownerName . "'s Minion";
	}

	/**
	 * Returns the minion's working radius.
	 */
	public function getWorkingRadius() : int{
		return $this->minionInformation->getUpgrade()->hasExpander() ? 3 : 2;
	}

	/**
	 * Returns the minion's information.
	 */
	public function getMinionInformation() : MinionInformation{
		return $this->minionInformation;
	}

	/**
	 * Returns the minion's inventory.
	 */
	public function getMinionInventory() : MinionInventory{
		return $this->minionInventory;
	}

	public function getTool() : Item{
		return match($this->minionInformation->getRealTarget()->getBreakInfo()->getToolType()){
			BlockToolType::AXE => VanillaItems::IRON_AXE(),
			BlockToolType::HOE => VanillaItems::IRON_HOE(),
			BlockToolType::PICKAXE => VanillaItems::IRON_PICKAXE(),
			BlockToolType::SHOVEL => VanillaItems::IRON_SHOVEL(),
			BlockToolType::SWORD => VanillaItems::IRON_SWORD(),
			BlockToolType::SHEARS => VanillaItems::SHEARS(),
			default => VanillaItems::AIR()
		};
	}
}
