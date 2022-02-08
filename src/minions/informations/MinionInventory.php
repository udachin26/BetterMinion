<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions\informations;

use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\Tag;
use function array_map;
use function get_class;

class MinionInventory extends SimpleInventory implements MinionNBT{
	public const MAX_SIZE = 15;
	public function setSize(int $size) : void{
		$this->slots->setSize($size);
	}

	public function nbtSerialize() : ListTag{
		return new ListTag(
			array_map(
				fn (Item $item) => $item->nbtSerialize(),
				$this->getContents()
			),
			NBT::TAG_Compound
		);
	}

	public static function nbtDeserialize(Tag $nbt) : self{
		if(!$nbt instanceof ListTag){
			throw new \InvalidArgumentException("Expected " . ListTag::class . ", got " . get_class($nbt));
		}
		$inventory = new self(self::MAX_SIZE);
		$inventory->setContents(
			array_map(
				fn (CompoundTag $tag) => Item::nbtDeserialize($tag),
				$nbt->getValue()
			)
		);
		return $inventory;
	}
}