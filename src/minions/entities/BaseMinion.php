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
	protected MinionInformation $minionInformation;
	protected MinionInventory $minionInventory;

	/**
	 * Constructor of the minions, I think.
	 */
	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->owner = Uuid::uuid3(Uuid::NIL, $nbt->getString(MinionNBT::OWNER));
		$infoNBT = $nbt->getCompoundTag(MinionNBT::INFORMATION);
		if($infoNBT === null){
			$this->flagForDespawn();
			return;
		}
		$this->minionInformation = MinionInformation::nbtDeserialize($infoNBT);
		$this->minionInventory = MinionInventory::nbtDeserialize(
			$nbt->getCompoundTag(MinionNBT::INVENTORY) ??
			new ListTag([], NBT::TAG_Compound)
		);
		$this->minionInventory->setSize($this->minionInformation->getLevel());
	}

	/**
	 * Save the minion's information.
	 */
	public function saveNBT() : CompoundTag{
		return parent::saveNBT()
			->setString(MinionNBT::OWNER, $this->owner->toString())
			->setTag(MinionNBT::INFORMATION, $this->minionInformation->nbtSerialize())
			->setTag(MinionNBT::INVENTORY, $this->minionInventory->nbtSerialize());
	}
}
