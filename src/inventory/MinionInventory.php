<?php
declare(strict_types=1);

namespace Mcbeany\BetterMinion\inventory;

use Mcbeany\BetterMinion\entities\BaseMinion;
use Mcbeany\BetterMinion\events\inventory\MinionInventoryChangedEvent;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;

class MinionInventory extends SimpleInventory{

	protected BaseMinion $minion;

	public function __construct(int $size, BaseMinion $minion){
		$this->minion = $minion;
		parent::__construct($size);
	}

	public function setItem(int $index, Item $item) : void{
		parent::setItem($index, $item);
		(new MinionInventoryChangedEvent($this))->call();
	}

	public function setContents(array $items) : void{
		parent::setContents($items);
		(new MinionInventoryChangedEvent($this))->call();
	}

	public function getMinion() : BaseMinion{
		return $this->minion;
	}
}