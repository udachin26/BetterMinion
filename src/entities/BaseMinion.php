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

	protected MinionInfo $minionInfo;
	protected SimpleInventory $minionInv;

	protected int $tickWait = 0;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->minionInfo = MinionInfo::nbtDeserialize($nbt->getCompoundTag(MinionNBT::INFO));
		$this->minionInv = new SimpleInventory($this->getMinionInfo()->getLevel());
		$this->getMinionInventory()->setContents(array_map(fn(CompoundTag $nbt) : Item => Item::nbtDeserialize($nbt), $nbt->getListTag(MinionNBT::INV)?->getValue() ?? []));
	}

	public function getMinionInfo() : MinionInfo{
		return $this->minionInfo;
	}

	public function getMinionInventory() : SimpleInventory{
		return $this->minionInv;
	}

	protected function onAction() : bool{
		return true;
	}

	public function onUpdate(int $currentTick) : bool{
		if ($this->tickWait < $this->getMinionInfo()->getActionTime()){
			$this->tickWait++;
		} else {
			$hasUpdate = $this->onAction();
			$this->tickWait = 0;
		}

		if (isset($hasUpdate)){
			return $hasUpdate;
		}
		return parent::onUpdate($currentTick);
	}
}