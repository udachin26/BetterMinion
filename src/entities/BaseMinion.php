<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities;

use Mcbeany\BetterMinion\minions\MinionInfo;
use Mcbeany\BetterMinion\minions\MinionNBT;
use pocketmine\block\Block;
use pocketmine\entity\Human;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;

abstract class BaseMinion extends Human{

	protected const WORKING_RADIUS = 2;

	protected MinionInfo $minionInfo;
	protected SimpleInventory $minionInv;

	protected int $tickWait = 0;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->minionInfo = MinionInfo::nbtDeserialize($nbt);
		$this->minionInv = new SimpleInventory($this->getMinionInfo()->getLevel());
		$this->getMinionInventory()->setContents(array_map(fn(CompoundTag $nbt) : Item => Item::nbtDeserialize($nbt), $nbt->getListTag(MinionNBT::INV)?->getValue() ?? []));
	}

    public function saveNBT() : CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->merge($this->getMinionInfo()->nbtSerialize());
        return $nbt;
    }

    public function getMinionInfo() : MinionInfo{
		return $this->minionInfo;
	}

	public function getMinionInventory() : SimpleInventory{
		return $this->minionInv;
	}

    public function getActionTime() : int{
        return 1; // TODO: Level-based action time
    }

	/**
	 * @return Block[]
	 */
	public function getWorkingBlocks() : array{
		return [];
	}

	protected function onAction() : bool{
		return true;
	}

	protected function doOfflineAction(int $times) : bool{
		return true;
	}

	protected function entityBaseTick(int $tickDiff = 1) : bool{
		$this->tickWait += $tickDiff;
		$actionTime = $this->getActionTime();
		if ($this->tickWait >= $actionTime){
			$times = $this->tickWait / $actionTime;
			$this->tickWait -= $actionTime * $times;
			if ($this->tickWait == 0){
				if (($times - 1) > 0){
					$this->doOfflineAction($times - 1);
				}
				$hasUpdate = $this->onAction();
			}else{
				$hasUpdate = $this->doOfflineAction($times);
			}
		}
		if (isset($hasUpdate)){
			return $hasUpdate;
		}
		return parent::entityBaseTick($tickDiff);
	}
}