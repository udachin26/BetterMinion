<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions;

use pocketmine\block\BlockIdentifier;
use pocketmine\nbt\tag\CompoundTag;

final class MinionInfo implements MinionNBT
{

    public function __construct(
        private MinionType $type,
        private BlockIdentifier $target,
        private MinionUpgrade $upgrade,
        private int $level,
        private float $moneyHeld,
        private int $collectedResources        
    ) {}

    public function getType(): MinionType
    {
        return $this->type;
    }

    public function getUpgrade(): MinionUpgrade
    {
        return $this->upgrade;
    }

    public function getTarget(): BlockIdentifier
    {
        return $this->target;
    }
    
    public function getLevel(): int
    {
        return $this->level;
    }

    public function incrementLevel(): void
    {
        $this->level++;
    }

    public function getMoneyHeld(): float
    {
        return $this->moneyHeld;
    }

    public function incrementMoneyHeld(float $moneyHeld): void
    {
        $this->moneyHeld += $moneyHeld;
    }

    public function getCollectedResources(): int
    {
        return $this->collectedResources;
    }

    public function incrementCollectedResources(int $collectedResources): void
    {
        $this->collectedResources += $collectedResources;
    }

    public function nbtSerialize(): CompoundTag
    {
        return CompoundTag::create()
            ->setString(MinionNBT::TYPE, $this->getType()->name())
            ->setTag(MinionNBT::TARGET, $this->targetSerialize())
            ->setTag(MinionNBT::UPGRADE, $this->getUpgrade()->nbtSerialize())
            ->setInt(MinionNBT::LEVEL, $this->getLevel())
            ->setFloat(MinionNBT::MONEY_HELD, $this->getMoneyHeld())
            ->setInt(MinionNBT::COLLECTED_RESOURCES, $this->getCollectedResources());
    }

    public static function nbtDeserialize(CompoundTag $nbt): self
    {
        return new self(
            MinionType::fromString($nbt->getString(MinionNBT::TYPE)),
            self::targetDeserialize($nbt->getCompoundTag(MinionNBT::TARGET)),
            MinionUpgrade::nbtDeserialize($nbt->getCompoundTag(MinionNBT::UPGRADE)),
            $nbt->getInt(MinionNBT::LEVEL),
            $nbt->getFloat(MinionNBT::MONEY_HELD),
            $nbt->getInt(MinionNBT::COLLECTED_RESOURCES)
        );
    }

    private function targetSerialize(): CompoundTag
    {
        return CompoundTag::create()
            ->setInt(MinionNBT::BLOCK_ID, $this->target->getBlockId())
            ->setInt(MinionNBT::VARIANT, $this->target->getVariant());
    }

    private static function targetDeserialize(CompoundTag $nbt): BlockIdentifier
    {
        return new BlockIdentifier(
            $nbt->getInt(MinionNBT::BLOCK_ID),
            $nbt->getInt(MinionNBT::VARIANT)
        );
    }

}