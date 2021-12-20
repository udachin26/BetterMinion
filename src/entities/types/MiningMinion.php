<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities\types;

use Mcbeany\BetterMinion\entities\BaseMinion;

class MiningMinion extends BaseMinion{
	public function getWorkingBlocks() : array{
		$blocks = [];
		$x = $this->getPosition()->getX();
		$y = $this->getPosition()->getY();
		$z = $this->getPosition()->getZ();
		for ($i = $x - self::WORKING_RADIUS; $i <= $x + self::WORKING_RADIUS; $i++){
			for ($j = $z - self::WORKING_RADIUS; $j <= $z + self::WORKING_RADIUS; $z++){
				if (($i !== $x) && ($j !== $z)){
					$blocks[] = $this->getPosition()->getWorld()->getBlockAt($i, $y - 1, $j);
				}
			}
		}
		return $blocks;
	}

	public function onAction() : bool{
		return parent::onAction();
	}
}