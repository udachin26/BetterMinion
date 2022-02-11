<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions\informations;

use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\utils\EnumTrait;
use function get_class;
use function mb_strtoupper;
use function ucfirst;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static MinionType MINING()
 */

/*
 * Basic type of the minions.
 */
class MinionType implements MinionNBT{
	use EnumTrait;

	// TODO: Add more types.
	protected static function setup() : void{
		self::registerAll(
			new self("mining")
		);
	}

	/**
	 * Gets type from name.
	 *
	 * @return MinionType|null Returns null if not found.
	 */
	public static function fromString(string $typeName) : ?self{
		self::checkInit();
		return self::$members[mb_strtoupper($typeName)] ?? null;
	}

	/**
	 * Returns the name of the type with first letter uppercased.
	 * Example: "mining" -> "Mining".
	 */
	public function typeName() : string{
		return ucfirst($this->name());
	}

	/**
	 * @see MinionNBT::nbtSerialize()
	 */
	public function nbtSerialize() : StringTag{
		return new StringTag($this->name());
	}

	/**
	 * @param StringTag $nbt
	 *
	 * @return MinionType
	 *
	 * @see MinionNBT::nbtDeserialize()
	 */
	public static function nbtDeserialize(Tag $nbt) : self{
		if(!$nbt instanceof StringTag){
			throw new \InvalidArgumentException("Expected " . StringTag::class . ", got " . get_class($nbt));
		}
		return self::fromString($nbt->getValue());
	}
}
