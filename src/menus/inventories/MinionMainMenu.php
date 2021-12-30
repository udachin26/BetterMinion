<?php

namespace Mcbeany\BetterMinion\menus\inventories;

use Mcbeany\BetterMinion\menus\InventoryMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;

class MinionMainMenu extends InventoryMenu{

	protected const TYPE = InvMenuTypeIds::TYPE_DOUBLE_CHEST;

	protected bool $readonly = true;

	public function render() : void{
		$this->getInvMenu()->getInventory()->setItem(53, VanillaBlocks::BEDROCK()->asItem());
	}

	public function onResponse(Player $player, $response){
		$player->sendMessage("bruh"); // TODO
	}

}