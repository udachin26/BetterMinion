<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions;

use pocketmine\nbt\tag\CompoundTag;

interface MinionNBT
{

    // Info
    public const INFO = "minionInfo";
    public const TYPE = "type";
    public const UPGRADE = "upgrade";
    public const LEVEL = "level";
    public const MONEY_HELD = "moneyHeld";
    public const COLLECTED_RESOURCES = "collectedResources";

    // Inv
    public const INV = "minionInv";

    // Upgrade
    public const AUTO_SMELTER = "autoSmelter";
    public const AUTO_SELLER = "autoSeller";
    public const COMPACTOR = "compactor";
    public const EXPANDER = "expander";

    public function nbtSerialize(): CompoundTag;

    public static function nbtDeserialize(CompoundTag $nbt);

}