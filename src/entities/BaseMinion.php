<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities;

use Mcbeany\BetterMinion\minions\MinionInfo;
use Mcbeany\BetterMinion\minions\MinionNBT;
use pocketmine\entity\Human;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;

abstract class BaseMinion extends Human{

	private MinionInfo $minionInfo;
	private SimpleInventory $minionInv;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->minionInfo = MinionInfo::nbtDeserialize($nbt->getCompoundTag(MinionNBT::INFO));
		$this->minionInv = new SimpleInventory($this->getMinionInfo()->getLevel());
		$this->getMinionInventory()->setContents(array_map(fn(CompoundTag $nbt) : Item => Item::nbtDeserialize($nbt),
			$nbt->getListTag(MinionNBT::INV)?->getValue() ?? []));
	}

	public function getMinionInfo() : MinionInfo{
		return $this->minionInfo;
	}

	public function getMinionInventory() : SimpleInventory{
		return $this->minionInv;
	}

}