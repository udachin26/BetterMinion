<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities\types;

use Mcbeany\BetterMinion\entities\BaseMinion;
use pocketmine\block\Air;
use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\Position;

class MiningMinion extends BaseMinion{
	public function getWorkingBlocks() : array{
		$blocks = [];
		$x = (int)$this->getPosition()->getX();
		$y = (int)$this->getPosition()->getY();
		$z = (int)$this->getPosition()->getZ();
		for($i = $x - self::WORKING_RADIUS; $i <= $x + self::WORKING_RADIUS; $i++){
			for($j = $z - self::WORKING_RADIUS; $j <= $z + self::WORKING_RADIUS; $z++){
				if(($i !== $x) && ($j !== $z)){
					$blocks[] = $this->getPosition()->getWorld()->getBlockAt($i, $y - 1, $j);
				}
			}
		}
		return $blocks;
	}

	protected function place(Position $position) : void{
		//TODO: Place animation
		$position->getWorld()->setBlock($position, $this->getMinionInfo()->getRealTarget());
	}

	protected function mine(Position $position) : void{
		//TODO: Mining animation
	}

	protected function onAction() : bool{
		if ($this->isContainInvalidBlock()){
			//TODO: Send minion message in his nametag like "This place isnt perfect :("
			return parent::onAction();
		}
		if ($this->isContainAir()){
			$pos = $this->getAirBlock()->getPosition();
			$this->place($pos);
			return parent::onAction();
		}
		$area = $this->getWorkingBlocks();
		$pos = $area[array_rand($area)]->getPosition();
		$this->mine($pos);
		return parent::onAction();
	}

	protected function doOfflineAction(int $times) : bool{
		//TODO: Offline action (just add stuff to inventory)
		return parent::doOfflineAction($times);
	}
}