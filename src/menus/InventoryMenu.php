<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\menus;

use Mcbeany\BetterMinion\BetterMinion;
use Mcbeany\BetterMinion\entities\BaseMinion;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;

abstract class InventoryMenu implements IMenu{

	protected const TYPE = InvMenuTypeIds::TYPE_CHEST;

	protected Task $renderTask;
	protected InvMenu $invMenu;

	protected string $name = "";
	protected bool $readonly = false;

	public function __construct(
		protected ?BaseMinion $minion = null
	){
		$onResponse = fn(InvMenuTransaction $transaction) => $this->onResponse($transaction->getPlayer(), $transaction);
		$this->invMenu = InvMenu::create(static::TYPE)
			->setName($this->name)
			->setListener($this->readonly ? InvMenu::readonly($onResponse) : $onResponse);
		$this->renderTask = new ClosureTask(\Closure::fromCallable([$this, 'onDisplay']));
		$this->getInvMenu()->setInventoryCloseListener(fn(Player $player, Inventory $inventory) => $this->onClose($player));
	}

	public function getMinion() : ?BaseMinion{
		return $this->minion;
	}

	public function getInvMenu() : InvMenu{
		return $this->invMenu;
	}

	public function getRenderTask() : Task{
		return $this->renderTask;
	}

	abstract public function render() : void;

	/**
	 * @param Player             $player
	 * @param InvMenuTransaction $response
	 *
	 * @return InvMenuTransactionResult|void
	 */
	abstract public function onResponse(Player $player, $response);

	public function onClose(Player $player) : void{
		$this->getRenderTask()->getHandler()->cancel();
	}

	public function forceClose(Player $player) : void{
		$player->removeCurrentWindow();
	}

	public function display(Player $player) : void{
		$this->getInvMenu()->send($player);
		BetterMinion::getInstance()->getScheduler()->scheduleRepeatingTask($this->getRenderTask(), 5); // 5 ticks
	}

	public function onDisplay() : void{
		$this->render();
	}

}