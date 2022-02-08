<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use pocketmine\plugin\PluginBase;

final class BetterMinion extends PluginBase{
	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}
}