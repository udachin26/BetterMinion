<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\events;

use Mcbeany\BetterMinion\entities\BaseMinion;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

abstract class MinionEvent extends PlayerEvent
{

    public function __construct(
        protected Player $player,
        protected BaseMinion $minion
    ) {}

    public function getMinion(): BaseMinion
    {
        return $this->minion;
    }

}