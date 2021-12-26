<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\events;

use Mcbeany\BetterMinion\entities\BaseMinion;
use Mcbeany\BetterMinion\sessions\Session;
use Mcbeany\BetterMinion\sessions\SessionManager;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

abstract class MinionEvent extends PlayerEvent{

	protected BaseMinion $minion;

	public function __construct(
		Player $player,
		BaseMinion $minion
	){
	}

	public function getMinion() : BaseMinion{
		return $this->minion;
	}

	public function isOwn() : bool{
		return $this->getMinion()->getOwner()->equals($this->getPlayer()->getUniqueId());
	}

	public function getSession() : ?Session{
		return SessionManager::getSession($this->getPlayer());
	}

}