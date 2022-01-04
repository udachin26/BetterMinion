<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\events;

use Mcbeany\BetterMinion\entities\BaseMinion;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class MinionWorkEvent extends MinionEvent implements Cancellable{
	use CancellableTrait;

	protected mixed $target;

	public function __construct(Player $player, BaseMinion $minion, $target){
		parent::__construct($player, $minion);
		$this->target = $target;
	}

	public function getTarget(){
		return $this->target;
	}

}