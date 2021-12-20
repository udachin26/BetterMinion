<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\minions;

use pocketmine\nbt\tag\CompoundTag;

final class MinionUpgrade implements MinionNBT{

	public function __construct(
		private bool $autoSmelter,
		private bool $autoSeller,
		private bool $compactor,
		private bool $expander
	){
	}

	public static function nbtDeserialize(CompoundTag $nbt) : self{
		return new self(
			(bool) $nbt->getByte(MinionNBT::AUTO_SMELTER),
			(bool) $nbt->getByte(MinionNBT::AUTO_SELLER),
			(bool) $nbt->getByte(MinionNBT::COMPACTOR),
			(bool) $nbt->getByte(MinionNBT::EXPANDER)
		);
	}

	public function nbtSerialize() : CompoundTag{
		return CompoundTag::create()
			->setByte(MinionNBT::AUTO_SMELTER, (int) $this->getAutoSmelter())
			->setByte(MinionNBT::AUTO_SELLER, (int) $this->getAutoSeller())
			->setByte(MinionNBT::COMPACTOR, (int) $this->getCompactor())
			->setByte(MinionNBT::EXPANDER, (int) $this->getExpander());
	}

	public function setAutoSmelter($autoSmelter = true) : void{
		$this->autoSmelter = $autoSmelter;
	}

	public function setAutoSeller($autoSeller = true) : void{
		$this->autoSeller = $autoSeller;
	}

	public function setCompactor($compactor = true) : void{
		$this->compactor = $compactor;
	}

	public function setExpander($expander = true) : void{
		$this->expander = $expander;
	}

	public function getAutoSmelter() : bool{
		return $this->autoSmelter;
	}

	public function getAutoSeller() : bool{
		return $this->autoSeller;
	}

	public function getCompactor() : bool{
		return $this->compactor;
	}

	public function getExpander() : bool{
		return $this->expander;
	}

}