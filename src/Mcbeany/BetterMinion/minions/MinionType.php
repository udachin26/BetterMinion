<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions;

use Mcbeany\BetterMinion\BetterMinion;
use Mcbeany\BetterMinion\entities\objects\MinionTree;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;

class MinionType implements MinionNBT
{

    const MINING_MINION = 0;
    const FARMING_MINION = 1;
    const LUMBERJACK_MINION = 2;
    const SLAYING_MINION = 3;
    const FISHING_MINION = 4;

    /** @var int */
    private $actionType;
    /** @var int */
    private $targetId;
    /** @var int */
    private $targetMeta;

    public function __construct(int $actionType, int $targetId, int $targetMeta = 0)
    {
        $this->actionType = $actionType;
        $this->targetId = $targetId;
        $this->targetMeta = $targetMeta;
    }

    public function getActionType(): int
    {
        return $this->actionType;
    }

    public function getTargetId(): int
    {
        return $this->targetId;
    }

    public function getTargetMeta(): int
    {
        return $this->targetMeta;
    }

    public function toBlock(): Block
    {
        return Block::get($this->getTargetId(), $this->getTargetMeta());
    }

    public function toEntity(Level $level, Vector3 $pos): Entity
    {
        $class = $this->getTargetName();
        return new $class($this->getTargetId(), $level, Entity::createBaseNBT($pos));
    }

    public function toTree(): MinionTree
    {
        return new MinionTree($this->toBlock());
    }

    public function getTargetName(): string
    {
        if ($this->getActionType() === self::SLAYING_MINION) {
            $class = new \ReflectionClass(Entity::class);
            $entities = $class->getStaticPropertyValue("knownEntities");
            return $entities[$this->getTargetId()];
        }
        return $this->toBlock()->getName();
    }

    public function nbtSerialize(): CompoundTag
    {
        return new CompoundTag("MinionType", [
            new IntTag("ActionType", $this->getActionType()),
            new IntTag("TargetId", $this->getTargetId()),
            new IntTag("TargetMeta", $this->getTargetMeta())
        ]);
    }

    public static function nbtDeserialize(CompoundTag $tag): self
    {
        return new self($tag->getInt("ActionType"), $tag->getInt("TargetId"), $tag->getInt("TargetMeta"));
    }

}