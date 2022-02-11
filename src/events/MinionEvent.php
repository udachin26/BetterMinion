<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\events;

use Mcbeany\BetterMinion\minions\entities\BaseMinion;
use pocketmine\event\Event;

/**
 * Base MinionEvent, usually called when there is action from the minion itself.
 */
abstract class MinionEvent extends Event{
	public function __construct(
		protected BaseMinion $minion
	) {
	}

	/**
	 * Returns the selected minion.
	 */
	public function getMinion() : BaseMinion{
		return $this->minion;
	}
}
