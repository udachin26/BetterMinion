<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities;

use JetBrains\PhpStorm\Pure;
use Mcbeany\BetterMinion\BetterMinion;
use Mcbeany\BetterMinion\entities\inventory\MinionInventory;
use Mcbeany\BetterMinion\minions\MinionInformation;
use Mcbeany\BetterMinion\utils\Utils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use onebone\economyapi\EconomyAPI;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class MinionEntity extends Human
{
    const ACTION_IDLE = 0;
    const ACTION_TURNING = 1;
    const ACTION_WORKING = 2;
    const ACTION_FULL_INVENTORY = 3;

    /** @var MinionInformation */
    protected $minionInformation;
    /** @var MinionInventory */
    protected $minionInventory;
    /** @var int */
    protected $currentAction = self::ACTION_IDLE;
    /** @var int */
    protected $currentActionTicks = 0;
    /** @var Block|Living|null */
    protected $target = null;
    /** @var float */
    protected $gravity = 0;

    protected function initEntity(): void
    {
        parent::initEntity();
        $this->setScale(0.5);
        $this->setImmobile();
        $this->setNameTagAlwaysVisible();
        $this->minionInformation = MinionInformation::nbtDeserialize($this->namedtag->getCompoundTag("MinionInformation"));
        $this->minionInventory = new MinionInventory(array(), $this->minionInformation->getLevel());
        $invTag = $this->namedtag->getListTag("MinionInventory");
        if ($invTag !== null) {
            $this->minionInventory->setContents(array_map(function (CompoundTag $tag): Item{
                return Item::nbtDeserialize($tag);
            }, $invTag->getValue()));
        }
    }

    public function saveNBT(): void
    {
        parent::saveNBT();
        $this->namedtag->setTag(new ListTag("MinionInventory", array_map(function (Item $item): CompoundTag {
            return $item->nbtSerialize();
        }, $this->minionInventory->getContents())));
    }

    public function attack(EntityDamageEvent $source): void
    {
        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();
            if ($damager instanceof Player) {
                if ($damager->getName() === $this->getMinionInformation()->getOwner()) {
                    $menu = InvMenu::create(MenuIds::TYPE_DOUBLE_CHEST);
                    $menu->setName($this->getMinionInformation()->getOwner() . "'s Minion " . Utils::getRomanNumeral($this->getMinionInformation()->getLevel()));
                    $menu->getInventory()->setContents(array_fill(0, 54, Item::get(BlockIds::INVISIBLE_BEDROCK, 7)->setCustomName(TextFormat::RESET)));
                    $menu->getInventory()->setItem(48, Item::get(BlockIds::CHEST)->setCustomName(TextFormat::GREEN . "Retrieve all results"));
                    $menu->getInventory()->setItem(50, Item::get(ItemIds::BOTTLE_O_ENCHANTING)->setCustomName(TextFormat::AQUA . "Level up your minion")->setLore([$this->getMinionInformation()->getLevel() < 15 ? TextFormat::YELLOW . "Cost: " . TextFormat::GREEN . $this->getLevelUpCost() : TextFormat::RED . "Reached max level!"]));
                    $menu->getInventory()->setItem(53, Item::get(BlockIds::BEDROCK)->setCustomName(TextFormat::RED . "Remove your minion"));
                    $taskId = BetterMinion::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($menu): void {
                        for ($i = 0; $i < 15; $i++) {
                            $menu->getInventory()->setItem((int)(21 + ($i % 5) + (9 * floor($i / 5))), $this->getMinionInventory()->slotExists($i) ? $this->getMinionInventory()->getItem($i) : Item::get(BlockIds::STAINED_GLASS, 14)->setCustomName(TextFormat::YELLOW . "Unlock at level " . TextFormat::GOLD . Utils::getRomanNumeral(($i + 1))));
                        }
                    }), 1)->getTaskId();
                    $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction): void {
                        $player = $transaction->getPlayer();
                        $itemClicked = $transaction->getItemClicked();
                        $action = $transaction->getAction();
                        switch ($action->getSlot()) {
                            case 48:
                                $player->removeWindow($action->getInventory());
                                foreach (array_reverse($this->getMinionInventory()->getContents(), true) as $slot => $item) {
                                    if ($player->getInventory()->canAddItem($item)) {
                                        $player->getInventory()->addItem($item);
                                        $this->getMinionInventory()->setItem($slot, Item::get(BlockIds::AIR));
                                    } else {
                                        $player->sendMessage(TextFormat::RED . "Your inventory is full, empty it before making a transaction");
                                    }
                                }
                                break;
                            case 50:
                                $player->removeWindow($action->getInventory());
                                if ($this->getMinionInformation()->getLevel() < 15) {
                                    if (EconomyAPI::getInstance()->myMoney($player) - $this->getLevelUpCost() >= 0) {
                                        EconomyAPI::getInstance()->reduceMoney($player, $this->getLevelUpCost());
                                        $this->getMinionInformation()->incrementLevel();
                                        $player->sendMessage(TextFormat::GREEN . "Your minion has been upgraded to level " . TextFormat::GOLD . Utils::getRomanNumeral($this->getMinionInformation()->getLevel()));
                                        $this->getMinionInventory()->setSize($this->getMinionInformation()->getLevel());
                                    } else {
                                        $player->sendMessage(TextFormat::RED . "You don't have enough economy to level up!");
                                    }
                                } else {
                                    $player->sendMessage(TextFormat::RED . "Your minion has reached the maximum level!");
                                }
                                break;
                            case 53:
                                $player->removeWindow($action->getInventory());
                                $this->destroy();
                                break;
                            default:
                                for ($i = 0; $i <= 15; $i++) {
                                    if ($i > $this->getMinionInformation()->getLevel() - 1) continue;
                                    $slot = (int)(21 + ($i % 5) + (9 * floor($i / 5)));
                                    if ($action->getSlot() === $slot) {
                                        if ($player->getInventory()->canAddItem($itemClicked)) {
                                            $player->getInventory()->addItem($itemClicked);
                                            $remaining = $itemClicked->getCount();
                                            /** @var Item $item */
                                            foreach (array_reverse($this->getMinionInventory()->all($itemClicked), true) as $slot => $item) {
                                                $itemCount = $item->getCount();
                                                $this->getMinionInventory()->setItem($slot, $item->setCount($itemCount - $remaining > 0 ? $itemCount - $remaining : 0));
                                                $remaining -= $itemCount;
                                                if ($remaining === 0) break;
                                            }
                                        } else {
                                            $player->removeWindow($action->getInventory());
                                            $player->sendMessage(TextFormat::RED . "Your inventory is full, empty it before making a transaction");
                                        }
                                    }
                                }
                                break;
                        }
                    }));
                    $menu->send($damager);
                    $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($taskId): void {
                        BetterMinion::getInstance()->getScheduler()->cancelTask($taskId);
                    });
                }
            }
        }
        $source->setCancelled();
    }

    public function addEffect(EffectInstance $effect): bool
    {
        return false;
    }

    protected function stopWorking()
    {
        $this->currentAction = self::ACTION_IDLE;
        $this->currentActionTicks = 0;
        $this->target = null;
    }

    private function destroy()
    {
        $this->minionInventory->dropContents($this->level, $this);
        $minionItem = Item::get(ItemIds::SKULL);
        $minionItem->setNamedTagEntry($this->minionInformation->nbtSerialize());
        $this->level->dropItem($this, $minionItem);
        $this->flagForDespawn();
    }

    private function getLevelUpCost(): int
    {
        $costs = (array) BetterMinion::getInstance()->getConfig()->get("levelup-costs");
        return (int) $costs[$this->getMinionInformation()->getLevel()];
    }

    protected function getMinionRange(): int
    {
        return $this->getMinionInformation()->getUpgrade()->isExpand() ? 3 : 2;
    }

    public function getMinionInformation(): MinionInformation
    {
        return $this->minionInformation;
    }

    public function getMinionInventory(): MinionInventory
    {
        return $this->minionInventory;
    }

}