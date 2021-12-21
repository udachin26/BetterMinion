<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities\types;

use Mcbeany\BetterMinion\entities\BaseMinion;

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

	protected function onAction() : bool{
		$working_blocks = $this->getWorkingBlocks();
		$target = $working_blocks[array_rand($working_blocks)];
		$this->lookAt($target->getPosition());
		//TODO: Mine Action
		return parent::onAction();
	}

	protected function doOfflineAction(int $times) : bool{
		//TODO: Offline action (just add stuff to inventory)
		return parent::doOfflineAction($times);
	}
}