<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\events\player;

use Mcbeany\BetterMinion\minions\entities\BaseMinion;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

// Base PlayerMinionEvent.
// I want it to extend MinionEvent, but why PHP :sadthonk:
abstract class PlayerMinionEvent extends PlayerEvent{
	public function __construct(
		Player $player,
		protected BaseMinion $minion
	) {
		$this->player = $player;
	}

	// Returns the selected minion.
	public function getMinion() : BaseMinion{
		return $this->minion;
	}
}
