<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities;

use Mcbeany\BetterMinion\events\MinionCollectResourcesEvent;
use Mcbeany\BetterMinion\inventory\MinionInventory;
use Mcbeany\BetterMinion\minions\MinionInfo;
use Mcbeany\BetterMinion\minions\MinionNBT;
use Mcbeany\BetterMinion\utils\Configuration;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\entity\Human;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function array_map;

abstract class BaseMinion extends Human{

	protected const MAX_TICKDIFF = 20;

	protected UuidInterface $owner;
	protected string $ownerName;
	protected MinionInfo $minionInfo;
	protected MinionInventory $minionInv;

	protected int $tickWait = 0;
	protected bool $isPaused = false;

	protected $gravity = 0;
	protected $gravityEnabled = false;
	public $canCollide = false;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->owner = Uuid::uuid3(Uuid::NIL, $nbt->getString(MinionNBT::OWNER));
		$this->ownerName = $nbt->getString(MinionNBT::OWNER_NAME);
		$info_nbt = $nbt->getCompoundTag(MinionNBT::INFO);
		if ($info_nbt === null){
			//Clear invalid minion.
			$this->flagForDespawn();
			return;
		}
		$this->minionInfo = MinionInfo::nbtDeserialize($info_nbt);
		$this->minionInv = new MinionInventory($this->getMinionInfo()->getLevel(), $this);
		$this->getMinionInventory()->setContents(array_map(
			fn(CompoundTag $nbt) : Item => Item::nbtDeserialize($nbt),
			$nbt->getListTag(MinionNBT::INV)?->getValue() ?? []
		));
		$this->setScale(Configuration::minion_size());
		$this->getInventory()->setItemInHand($this->getTool());

		$this->setNameTag($this->getOriginalNameTag());
		$this->setNameTagAlwaysVisible(true);
	}

	public function saveNBT() : CompoundTag{
		return parent::saveNBT()
			->setString(MinionNBT::OWNER, $this->getOwner()->toString())
			->setString(MinionNBT::OWNER_NAME, $this->getOwnerName())
			->setTag(MinionNBT::INFO, $this->getMinionInfo()->nbtSerialize())
			->setTag(MinionNBT::INV, new ListTag(array_map(
				fn(Item $item) : CompoundTag => $item->nbtSerialize(),
				$this->getMinionInventory()->getContents(true)),
				NBT::TAG_Compound
			));
	}

	public function getOwner() : UuidInterface{
		return $this->owner;
	}

	public function getOwnerName() : string{
		return $this->ownerName;
	}

	public function getMinionInfo() : MinionInfo{
		return $this->minionInfo;
	}

	public function getMinionInventory() : MinionInventory{
		return $this->minionInv;
	}

	public function getActionTime() : int{
		return 100; // TODO: Level-based action time
	}

	public function getOriginalNameTag() : string{
		//TODO: Custom Nametag via Configuration.
		return $this->getOwnerName() . "'s Minion";
	}

	/**
	 * @return Block[]
	 */
	public function getWorkingBlocks() : array{
		return [];
	}

	protected function isContainAir() : bool{
		$workspace = $this->getWorkingBlocks();
		foreach($workspace as $block){
			if($block instanceof Air){
				return true;
			}
		}
		return false;
	}

	protected function getAirBlock() : ?Air{
		$workspace = $this->getWorkingBlocks();
		foreach($workspace as $block){
			if($block instanceof Air){
				return $block;
			}
		}
		return null;
	}

	protected function isContainInvalidBlock() : bool{
		$workspace = $this->getWorkingBlocks();
		foreach($workspace as $block){
			if(!$block->isSameType($this->getMinionInfo()->getRealTarget())){
				if(!$block instanceof Air){
					return true;
				}
			}
		}
		return false;
	}

	protected function onAction() : void{
	}

	protected function doOfflineAction(int $times) : void{
	}

	protected function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->isStopedWorking()){
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

	protected function minionAnimationTick(int $tickDiff = 1){
	}

	protected function getTool() : Item{
		return ItemFactory::air();
	}

	public function stopWorking() : void{
		$this->isPaused = true;
	}

	public function continueWorking() : void{
		$this->isPaused = false;
	}

	public function isStopedWorking() : bool{
		return $this->isPaused;
	}

	protected function getWorkingRadius() : int{
		//TODO: Expander upgrade
		return 2;
	}

	/**
	 * @param Item[] $drops
	 */
	protected function addStuff(array $drops) : void{
		foreach($drops as $drop){
			if (!$this->getMinionInventory()->canAddItem($drop)){
				//TODO: Inventory Full Alert
				return;
			}
			$event = new MinionCollectResourcesEvent($this);
			$event->call();
			if (!$event->isCancelled()){
				$this->getMinionInventory()->addItem($drop);
				$this->getMinionInfo()->incrementCollectedResources($drop->getCount());
			}
		}
	}
}