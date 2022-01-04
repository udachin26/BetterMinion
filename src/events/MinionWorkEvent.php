<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\events;

use Mcbeany\BetterMinion\entities\BaseMinion;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class MinionWorkEvent extends MinionEvent implements Cancellable{
	use CancellableTrait;

	public function __construct(Player $player, BaseMinion $minion, protected mixed $target){
		parent::__construct($player, $minion);
	}

	public function getTarget(){
		return $this->target;
	}

}