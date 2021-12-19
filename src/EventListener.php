<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

final class EventListener implements Listener
{
    
    public function onItemUse(PlayerItemUseEvent $event): void
    {}

}
