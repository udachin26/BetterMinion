<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\menus;

use Mcbeany\BetterMinion\BetterMinion;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;

abstract class InventoryMenu implements IMenu{

	protected const TYPE = InvMenuTypeIds::TYPE_CHEST;

	protected InvMenu $invMenu;
	protected Task $renderTask;

	protected function __construct(){
		$this->invMenu = InvMenu::create(static::TYPE); // TODO: Readonly menu
		$this->renderTask = new ClosureTask(function() : void{
			$this->render();
		});

		$this->invMenu->setName($this->getName());
		$this->invMenu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
			return $this->onResponse($transaction->getPlayer(), $transaction);
		});
		$this->invMenu->setInventoryCloseListener(function(Player $player, Inventory $inventory) : void{
			$this->onClose($player);
		});
	}

	abstract protected function getName() : string;


	/**
	 * @param Player             $player
	 * @param InvMenuTransaction $response
	 *
	 * @return InvMenuTransactionResult
	 */
	abstract public function onResponse(Player $player, $response) : InvMenuTransactionResult;

	public function onClose(Player $player) : void{
	}

	public function forceClose(Player $player): void{
		$player->removeCurrentWindow();
	}

	public function display(Player $player) : void{
		$this->invMenu->send($player);
		BetterMinion::getInstance()->getScheduler()->scheduleRepeatingTask($this->renderTask, 1); // 1 tick (magic number lol)
	}

}