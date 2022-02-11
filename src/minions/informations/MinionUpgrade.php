<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions\informations;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use function get_class;

/**
 * Some basic upgrades of the minions which are copied from Hypixel :D
 * Method names are self-explanatory
 */
class MinionUpgrade implements MinionNBT{
	public function __construct(
		private bool $autoSmelter = false,
		private bool $autoSeller = false,
		private bool $compactor = false,
		private bool $expander = false,
	) {
	}

	public function hasAutoSmelter() : bool{
		return $this->autoSmelter;
	}

	public function hasAutoSeller() : bool{
		return $this->autoSeller;
	}

	public function hasCompactor() : bool{
		return $this->compactor;
	}

	public function hasExpander() : bool{
		return $this->expander;
	}

	public function setAutoSmelter(bool $autoSmelter = true) : void{
		$this->autoSmelter = $autoSmelter;
	}

	public function setAutoSeller(bool $autoSeller = true) : void{
		$this->autoSeller = $autoSeller;
	}

	public function setCompactor(bool $compactor = true) : void{
		$this->compactor = $compactor;
	}

	public function setExpander(bool $expander = true) : void{
		$this->expander = $expander;
	}

	/**
	 * @return CompoundTag
	 *
	 * @see MinionNBT::nbtSerialize()
	 */
	public function nbtSerialize() : CompoundTag{
		return CompoundTag::create()
			->setByte(MinionNBT::AUTO_SMELTER, (int) $this->autoSmelter)
			->setByte(MinionNBT::AUTO_SELLER, (int) $this->autoSeller)
			->setByte(MinionNBT::COMPACTOR, (int) $this->compactor)
			->setByte(MinionNBT::EXPANDER, (int) $this->expander);
	}

	/*
	 * @param CompoundTag $nbt
	 *
	 * @return MinionUpgrade
	 *
	 * @see MinionNBT::nbtDeserialize()
	 */
	public static function nbtDeserialize(Tag $nbt) : self{
		if(!$nbt instanceof CompoundTag){
			throw new \InvalidArgumentException("Expected " . CompoundTag::class . ", got " . get_class($nbt));
		}
		return new self(
			(bool) $nbt->getByte(MinionNBT::AUTO_SMELTER),
			(bool) $nbt->getByte(MinionNBT::AUTO_SELLER),
			(bool) $nbt->getByte(MinionNBT::COMPACTOR),
			(bool) $nbt->getByte(MinionNBT::EXPANDER)
		);
	}
}
