<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities\objects;

use pocketmine\block\Farmland as PMFarmLand;

class Farmland extends PMFarmLand
{

    private $fromMinion;

    public function __construct(int $meta = 0, bool $fromMinion = false)
    {
        parent::__construct($meta);
        $this->fromMinion = $fromMinion;
    }

    protected function canHydrate(): bool
    {
        if ($this->fromMinion) return true;
        return parent::canHydrate();
    }

}