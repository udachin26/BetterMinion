<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities\types;

use Mcbeany\BetterMinion\entities\BaseMinion;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Facing;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\BlockPunchParticle;
use pocketmine\world\Position;
use pocketmine\world\sound\BlockPunchSound;

class MiningMinion extends BaseMinion{

	protected bool $isMining = false;
	protected int $miningTimer = 0;
	protected ?Block $mining_block = null;

	public function getWorkingBlocks() : array{
		$blocks = [];
		$x = (int) $this->getPosition()->getX();
		$y = (int) $this->getPosition()->getY();
		$z = (int) $this->getPosition()->getZ();
		for($i = $x - $this->getWorkingRadius(); $i <= $x + $this->getWorkingRadius(); $i++){
			for($j = $z - $this->getWorkingRadius(); $j <= $z + $this->getWorkingRadius(); $j++){
				if(($i == $x) && ($j == $z)){
					continue;
				}
				$blocks[] = $this->getPosition()->getWorld()->getBlockAt($i, $y - 1, $j);
			}
		}
		return $blocks;
	}

	protected function place(Position $position) : void{
		$this->lookAt($position);
		$this->getInventory()->setItemInHand($this->getMinionInfo()->getRealTarget()->asItem());
		$this->broadcastAnimation(new ArmSwingAnimation($this), $this->getViewers());
		$position->getWorld()->setBlock($position, $this->getMinionInfo()->getRealTarget());
	}

	protected function startMine(Block $block) : void{
		$this->getInventory()->setItemInHand($this->getTool());
		$this->isMining = true;
		$this->mining_block = $block;
		$breakTime = $this->getMinionInfo()->getRealTarget()->getBreakInfo()->getBreakTime($this->getTool());
		$breakSpeed = $breakTime * 20;
		$this->miningTimer = (int) $breakSpeed;
		if($this->miningTimer > $this->getActionTime()){ //When mining time > action time will cause spaming breaking block ...
			$this->stopWorking();
			$this->setNameTag($this->getOriginalNameTag() . "\nThe block break time too long :(");
			return;
		}
		if($breakSpeed > 0){
			$breakSpeed = 1 / $breakSpeed;
		}else{
			$breakSpeed = 1;
		}
		$this->lookAt($block->getPosition());
		$block->getPosition()->getWorld()->broadcastPacketToViewers($block->getPosition(), LevelEventPacket::create(LevelEvent::BLOCK_START_BREAK, (int) (65535 * $breakSpeed), $block->getPosition()));
	}

	protected function mine(){
		$this->broadcastAnimation(new ArmSwingAnimation($this), $this->getViewers());
		$this->getWorld()->addParticle($this->mining_block->getPosition(), new BlockPunchParticle($this->mining_block, Facing::opposite($this->getHorizontalFacing())));
		$this->broadcastSound(new BlockPunchSound($this->mining_block), $this->getViewers());
	}

	protected function onAction() : bool{
		if($this->isContainInvalidBlock()){
			$this->setNameTag($this->getOriginalNameTag() . "\nThis place doesnt perfect :(");
			return parent::onAction();
		}
		$this->setNameTag($this->getOriginalNameTag());
		if($this->isContainAir()){
			$pos = $this->getAirBlock()->getPosition();
			$this->place($pos);
			return parent::onAction();
		}
		if($this->mining_block == null){
			$area = $this->getWorkingBlocks();
			$block = $area[array_rand($area)];
			$this->startMine($block);
		}
		return parent::onAction();
	}

	protected function doOfflineAction(int $times) : bool{
		for ($i = 0; $i < $times; $i++){
			$this->addStuff();
		}
		return parent::doOfflineAction($times);
	}

	protected function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->isStopedWorking()){
			return true;
		}
		if($this->mining_block !== null){
			if($this->miningTimer - $tickDiff > 0){
				$this->miningTimer -= $tickDiff;
				$this->mine();
				return parent::entityBaseTick($tickDiff);
			}
			if($this->miningTimer - $tickDiff > self::MAX_TICKDIFF * (-1)){
				$this->miningTimer = 0;
				$block = clone $this->mining_block;
				$this->mining_block = null;
				$this->getWorld()->addParticle($block->getPosition()->add(0.5, 0.5, 0.5), new BlockBreakParticle($block));
				$this->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
				$this->addStuff();
				return parent::entityBaseTick($tickDiff);
			}
			if($this->miningTimer - $tickDiff < self::MAX_TICKDIFF * (-1)){
				$this->miningTimer = 0;
				//TODO: Hacks... Skip and just add stuff like offline action
				$this->mining_block = null;
				$this->doOfflineAction(1);
				return parent::entityBaseTick($tickDiff);
			}
		}
		return parent::entityBaseTick($tickDiff);
	}

	protected function getTool() : Item{
		return VanillaItems::DIAMOND_PICKAXE();
		//TODO: Custom for mining minion using shovel
	}

	protected function addStuff() : void{
		$drops = $this->getMinionInfo()->getRealTarget()->getDrops($this->getTool());
		foreach($drops as $drop){
			if (!$this->getMinionInventory()->canAddItem($drop)){
				//TODO: Inventory Full Alert
				return;
			}
			//TODO: Call event.
			$this->getMinionInventory()->addItem($drop);
		}
	}
}
