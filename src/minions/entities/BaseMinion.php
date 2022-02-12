<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions\entities;

use Mcbeany\BetterMinion\minions\informations\MinionInformation;
use Mcbeany\BetterMinion\minions\informations\MinionInventory;
use Mcbeany\BetterMinion\minions\informations\MinionNBT;
use pocketmine\entity\Human;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Base class for all minions.
 */
abstract class BaseMinion extends Human{
	protected UuidInterface $owner;
	protected string $ownerName;
	protected MinionInformation $minionInformation;
	protected MinionInventory $minionInventory;

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
			$nbt->getCompoundTag(MinionNBT::INVENTORY) ??
			new ListTag([], NBT::TAG_Compound)
		);
		$this->minionInventory->setSize($this->minionInformation->getLevel());
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

	public function onUpdate(int $currentTick) : bool{
		if($this->isWorking){
			$lastItem = $this->minionInventory->getItem($this->minionInventory->getSize() - 1);
			if(!$lastItem->isNull() && $lastItem->getCount() == $lastItem->getMaxStackSize()){
				$this->isWorking = false;
				$this->setNameTag("My inventory is full :<");
				return true;
			}
		}else{
			$this->isWorking = true;
		}
		$this->setNameTag();
		return parent::onUpdate($currentTick);
	}

	public function setNameTag(string $name = "") : void{
		if(empty($name)){
			$this->setNameTagVisible(false);
		}
		$this->setNameTagVisible();
		parent::setNameTag($name);
	}

	/**
	 * Get the owner of the minion.
	 */
	public function getOwner() : UuidInterface{
		return $this->owner;
	}

	/**
	 * Get the owner's name of the minion.
	 */
	public function getOwnerName() : string{
		return $this->ownerName;
	}

	/**
	 * @return string Returns original name of the minion, include its owner.
	 */
	public function getOriginalNameTag() : string{
		return $this->ownerName . "'s Minion";
	}

	/**
	 * Get the minion's information.
	 */
	public function getMinionInformation() : MinionInformation{
		return $this->minionInformation;
	}

	/**
	 * Get the minion's inventory.
	 */
	public function getMinionInventory() : MinionInventory{
		return $this->minionInventory;
	}
}
