<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\menus\inventories;

use Mcbeany\BetterMinion\BetterMinion;
use Mcbeany\BetterMinion\entities\BaseMinion;
use Mcbeany\BetterMinion\events\inventory\MinionInventoryChangedEvent;
use Mcbeany\BetterMinion\menus\InventoryMenu;
use Mcbeany\BetterMinion\menus\MinionMenuTrait;
use Mcbeany\BetterMinion\utils\Language;
use Mcbeany\BetterMinion\utils\Utils;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\HandlerListManager;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use function array_fill;
use function count;
use function floor;

class MinionMainMenu extends InventoryMenu implements Listener{
	use MinionMenuTrait {
		__construct as private __constructMinionMenu;
	}

	protected const TYPE = InvMenuTypeIds::TYPE_DOUBLE_CHEST;

	protected bool $readonly = true;

	public function __construct(?BaseMinion $minion = null){
		parent::__construct();
		$this->name = $minion?->getOriginalNameTag() ?? "";
		$this->__constructMinionMenu($minion);
		BetterMinion::getInstance()->getServer()->getPluginManager()->registerEvents($this, BetterMinion::getInstance());
	}

	public function render() : void{
		$inv = $this->getInvMenu()->getInventory();
		$inv->setContents(array_fill(0, 54, VanillaBlocks::INVISIBLE_BEDROCK()->asItem()->setCustomName("§k"))); //TODO: Hacks (This make item name become empty like "")
		for($i = 0; $i < 15; $i++){
			$invItem = $this->getMinion()->getMinionInventory()->slotExists($i) ?
				$this->getMinion()->getMinionInventory()->getItem($i) :
				VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem()->setCustomName("Unlock at level " . $i);
			$inv->setItem((int) (21 + ($i % 5) + (9 * (floor($i / 5)))), $invItem);
		}
		$info_item = VanillaItems::PLAYER_HEAD()->setCustomName("§r§f" . $this->getMinion()->getOriginalNameTag());
		$info_item->setLore([
			"§r§fTier: " . Utils::getRomanNumeral($this->getMinion()->getMinionInfo()->getLevel()),
			"§r§fCollected Resources: " . $this->getMinion()->getMinionInfo()->getCollectedResources(),
		]);
		$inv->setItem(4, $info_item);
		$inv->setItem(10, VanillaBlocks::FURNACE()->asItem()->setCustomName("AutoSmelter")->setLore(["Comming soon !"]));
		$inv->setItem(19, VanillaBlocks::HOPPER()->asItem()->setCustomName("AutoSell")->setLore(["Comming soon !"]));
		$inv->setItem(28, VanillaBlocks::LEGACY_STONECUTTER()->asItem()->setCustomName("Compactor")->setLore(["Comming soon !"]));
		$inv->setItem(37, BlockFactory::getInstance()->get(BlockLegacyIds::COMMAND_BLOCK, 0)->asItem()->setCustomName("Expander")->setLore(["Comming soon !"]));
		$inv->setItem(48, VanillaBlocks::CHEST()->asItem()->setCustomName("Retrieve all results"));
		$inv->setItem(53, VanillaBlocks::BEDROCK()->asItem()->setCustomName("Remove your minion"));
	}

	public function onResponse(Player $player, $response){
		switch($response->getAction()->getSlot()){
			case 48:
				$notFit = $player->getInventory()->addItem(...$this->getMinion()->getMinionInventory()->getContents());
				$this->getMinion()->getMinionInventory()->clearAll();
				if(count($notFit) > 0){
					$player->sendMessage(Language::inventory_is_full());
					$this->getMinion()->getMinionInventory()->addItem(...$notFit);
					$this->forceClose($player);
				}
				break;
			case 53:
				$info = $this->getMinion()->getMinionInfo();
				$spawner = BetterMinion::getInstance()->createSpawner(
					$info->getType(),
					$info->getTarget(),
					$info->getLevel(),
					$info->getMoneyHeld(),
					$info->getCollectedResources()
				);
				if($player->getInventory()->canAddItem($spawner)){
					$this->getMinion()->flagForDespawn();
					$player->getInventory()->addItem($spawner);
				}else{
					$player->sendMessage(Language::inventory_is_full());
				}
				$this->forceClose($player);
				break;
		}
	}

	public function onClose(Player $player) : void{
		HandlerListManager::global()->unregisterAll($this);
	}

	public function onMinionInventoryChanged(MinionInventoryChangedEvent $event) : void{
		$this->render();
	}

}