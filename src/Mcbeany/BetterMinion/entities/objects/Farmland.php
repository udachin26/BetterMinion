<?php

namespace Mcbeany\BetterMinion\entities\objects;

use pocketmine\block\Farmland as PMFarmland;

class Farmland extends PMFarmland
{

    /** @var bool */
    private $fromMinion;

    public function __construct(int $meta = 0, bool $fromMinion = false)
    {
        $this->fromMinion = $fromMinion;
        parent::__construct($meta);
    }

    protected function canHydrate(): bool
    {
        if ($this->fromMinion) return true;
        return parent::canHydrate();
    }

}