<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\menus\inventories;

use Mcbeany\BetterMinion\entities\BaseMinion;
use Mcbeany\BetterMinion\menus\InventoryMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;

class MinionMainMenu extends InventoryMenu{

	protected const TYPE = InvMenuTypeIds::TYPE_DOUBLE_CHEST;

	public function __construct(
		private BaseMinion $minion
	){
		parent::__construct();
	}

	public function render() : void{
		$this->invMenu->getInventory()->setItem(53, VanillaBlocks::BEDROCK()->asItem());
		// TODO: i forgor 💀
	}

	protected function getName() : string{
		return $this->minion->getOwnerName() . "'s Minion";
	}

	public function onResponse(Player $player, $response) : InvMenuTransactionResult{
		switch($response->getAction()->getSlot()) {
			case 53:
				$this->forceClose($player);
				break;
				// TODO
		}
		return $response->discard();
	}
}