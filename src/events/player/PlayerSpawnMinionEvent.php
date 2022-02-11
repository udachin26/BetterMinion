<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\events\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

/**
 * This event will be called when a player attempts to spawn a minion.
 *
 * @see MinionFactory::spawnMinion()
 */
class PlayerSpawnMinionEvent extends PlayerMinionEvent implements Cancellable{
	use CancellableTrait;
}
