<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions\informations;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use function get_class;

/**
 * Basic information about the minions include: type, target, upgrades, etc.
 * Information can be initialized from NBT.
 */
class MinionInformation implements MinionNBT{
	public function __construct(
		private MinionType $type,
		private BlockIdentifier $target,
		private MinionUpgrade $upgrade,
		private int $level
		// TODO
	) {
	}

	/**
	 * Returns the type of the minions.
	 */
	public function getType() : MinionType{
		return $this->type;
	}

	/**
	 * Returns target block.
	 */
	public function getTarget() : BlockIdentifier{
		return $this->target;
	}

	/**
	 * Returns real target as a Block.
	 */
	public function getRealTarget() : Block{
		/** @var Block $block */
		$block = BlockFactory::getInstance()->get(
			$this->target->getBlockId(),
			$this->target->getVariant()
		);
		return $block;
	}

	/**
	 * Returns the upgrades of the minions.
	 */
	public function getUpgrade() : MinionUpgrade{
		return $this->upgrade;
	}

	/**
	 * Returns the level of the minions.
	 */
	public function getLevel() : int{
		return $this->level;
	}

	/**
	 * Increases the level of the minions.
	 */
	public function increaseLevel() : void{
		$this->level++;
	}

	/**
	 * Serializes the target of the minions to NBT.
	 */
	protected function targetSerialize() : CompoundTag{
		return CompoundTag::create()
			->setInt(MinionNBT::BLOCK_ID, $this->target->getBlockId())
			->setInt(MinionNBT::VARIANT, $this->target->getVariant());
	}

	/**
	 * Deserializes the target of the minions from NBT.
	 */
	protected static function targetDeserialize(CompoundTag $nbt) : BlockIdentifier{
		return new BlockIdentifier(
			$nbt->getInt(MinionNBT::BLOCK_ID),
			$nbt->getInt(MinionNBT::VARIANT)
		);
	}

	/**
	 * @see MinionNBT::nbtSerialize()
	 */
	public function nbtSerialize() : CompoundTag{
		return CompoundTag::create()
			->setTag(MinionNBT::TYPE, $this->type->nbtSerialize())
			->setTag(MinionNBT::TARGET, $this->targetSerialize())
			->setTag(MinionNBT::UPGRADE, $this->upgrade->nbtSerialize())
			->setInt(MinionNBT::LEVEL, $this->level);
	}

	/**
	 * @param CompoundTag $nbt
	 *
	 * @return MinionInformation
	 *
	 * @see MinionNBT::nbtDeserialize()
	 */
	public static function nbtDeserialize(Tag $nbt) : self{
		if(!$nbt instanceof CompoundTag){
			throw new \InvalidArgumentException("Expected " . CompoundTag::class . ", got " . get_class($nbt));
		}
		return new self(
			MinionType::nbtDeserialize($nbt->getTag(MinionNBT::TYPE)),
			self::targetDeserialize($nbt->getTag(MinionNBT::TARGET)),
			MinionUpgrade::nbtDeserialize($nbt->getTag(MinionNBT::UPGRADE)),
			$nbt->getInt(MinionNBT::LEVEL)
		);
	}
}
