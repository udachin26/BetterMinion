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
	public const MIN_LEVEL = 1;
	public const MAX_LEVEL = 15;
	public function __construct(
		private MinionType $type,
		private BlockIdentifier $target,
		private MinionUpgrade $upgrade,
		private int $level = self::MIN_LEVEL
		// TODO
	) {
	}

	/**
	 * Returns type of the minions.
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
	 * Returns upgrades of the minion.
	 */
	public function getUpgrade() : MinionUpgrade{
		return $this->upgrade;
	}

	/**
	 * Returns level of the minions.
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
	 * Serializes target of the minions to NBT.
	 */
	protected function targetSerialize() : CompoundTag{
		return CompoundTag::create()
			->setInt(MinionNBT::BLOCK_ID, $this->target->getBlockId())
			->setInt(MinionNBT::VARIANT, $this->target->getVariant());
	}

	/**
	 * Deserializes target of the minions from NBT.
	 */
	protected static function targetDeserialize(CompoundTag $tag) : BlockIdentifier{
		return new BlockIdentifier(
			$tag->getInt(MinionNBT::BLOCK_ID),
			$tag->getInt(MinionNBT::VARIANT)
		);
	}

	/**
	 * @see MinionNBT::serializeTag()
	 */
	public function serializeTag() : CompoundTag{
		return CompoundTag::create()
			->setTag(MinionNBT::TYPE, $this->type->serializeTag())
			->setTag(MinionNBT::TARGET, $this->targetSerialize())
			->setTag(MinionNBT::UPGRADE, $this->upgrade->serializeTag())
			->setInt(MinionNBT::LEVEL, $this->level);
	}

	/**
	 * @param CompoundTag $tag
	 *
	 * @return MinionInformation
	 *
	 * @see MinionNBT::deserializeTag()
	 */
	public static function deserializeTag(Tag $tag) : self{
		if(!$tag instanceof CompoundTag){
			throw new \InvalidArgumentException("Expected " . CompoundTag::class . ", got " . get_class($tag));
		}
		return new self(
			MinionType::deserializeTag($tag->getTag(MinionNBT::TYPE)),
			self::targetDeserialize($tag->getTag(MinionNBT::TARGET)),
			MinionUpgrade::deserializeTag($tag->getTag(MinionNBT::UPGRADE)),
			$tag->getInt(MinionNBT::LEVEL)
		);
	}
}
