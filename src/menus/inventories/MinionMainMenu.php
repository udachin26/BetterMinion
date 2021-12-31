<?php

namespace Mcbeany\BetterMinion\menus\inventories;

use Mcbeany\BetterMinion\menus\InventoryMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;

class MinionMainMenu extends InventoryMenu{

	protected const TYPE = InvMenuTypeIds::TYPE_DOUBLE_CHEST;

	protected bool $readonly = true;

	public function render() : void{
		$inv = $this->getInvMenu()->getInventory();
		$inv->setContents(array_fill(0, 54, VanillaBlocks::INVISIBLE_BEDROCK()->asItem()->setCustomName("")));
		for($i = 0; $i < 15; $i++) {
			$invItem = $this->getMinion()->getMinionInventory()->slotExists($i) ?
				$this->getMinion()->getMinionInventory()->getItem($i) :
				VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem()->setCustomName("Unlock at level " . $i);
			$inv->setItem((int) (21 + ($i % 5) + (9 * (floor($i / 5)))), $invItem);
		}
		$inv->setItem(48, VanillaBlocks::CHEST()->asItem()->setCustomName("Retrieve all results"));
		$inv->setItem(53, VanillaBlocks::BEDROCK()->asItem()->setCustomName("Remove your minion"));
	}

	public function onDisplay() : void{
		parent::onDisplay();
	}

	public function onResponse(Player $player, $response){
		switch($response->getAction()->getSlot()) {
			case 48:
				$player->getInventory()->addItem(...$this->getMinion()->getMinionInventory()->getContents());
				break;
			case 53:
				$this->getMinion()->flagForDespawn();
				$this->forceClose($player);
				break;
		}
	}

}