<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions\informations;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use function get_class;

class MinionInformation implements MinionNBT{
	public function __construct(
		private MinionType $type,
		private BlockIdentifier $target,
		private MinionUpgrade $upgrade,
		private int $level
		// TODO
	) {
	}

	public function getType() : MinionType{
		return $this->type;
	}

	public function getTarget() : BlockIdentifier{
		return $this->target;
	}

	public function getRealTarget() : Block{
		/** @var Block $block */
		$block = BlockFactory::getInstance()->get(
			$this->target->getBlockId(),
			$this->target->getVariant()
		);
		return $block;
	}

	public function getUpgrade() : MinionUpgrade{
		return $this->upgrade;
	}

	public function getLevel() : int{
		return $this->level;
	}

	public function incrementLevel() : void{
		$this->level++;
	}

	protected function targetSerialize() : CompoundTag{
		return CompoundTag::create()
			->setInt(self::BLOCK_ID, $this->target->getBlockId())
			->setInt(self::VARIANT, $this->target->getVariant());
	}

	protected static function targetDeserialize(CompoundTag $nbt) : BlockIdentifier{
		return new BlockIdentifier(
			$nbt->getInt(self::BLOCK_ID),
			$nbt->getInt(self::VARIANT)
		);
	}

	public function nbtSerialize() : CompoundTag{
		return CompoundTag::create()
			->setTag(self::TYPE, $this->type->nbtSerialize())
			->setTag(self::TARGET, $this->targetSerialize())
			->setTag(self::UPGRADE, $this->upgrade->nbtSerialize())
			->setInt(self::LEVEL, $this->level);
	}

	public static function nbtDeserialize(Tag $nbt) : self{
		if(!$nbt instanceof CompoundTag){
			throw new \InvalidArgumentException("Expected " . CompoundTag::class . ", got " . get_class($nbt));
		}
		return new self(
			MinionType::nbtDeserialize($nbt->getTag(self::TYPE)),
			self::targetDeserialize($nbt->getTag(self::TARGET)),
			MinionUpgrade::nbtDeserialize($nbt->getTag(self::UPGRADE)),
			$nbt->getInt(self::LEVEL)
		);
	}
}