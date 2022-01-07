<?php
declare(strict_types=1);

namespace Mcbeany\BetterMinion\events\inventory;

use Mcbeany\BetterMinion\inventory\MinionInventory;
use pocketmine\event\inventory\InventoryEvent;

class MinionInventoryEvent extends InventoryEvent{

	public function __construct(MinionInventory $inventory){
		parent::__construct($inventory);
	}
}