<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities\types;

use Mcbeany\BetterMinion\entities\MinionEntity;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class MiningMinion extends MinionEntity
{

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $hasUpdate = parent::entityBaseTick($tickDiff);
        if (!$this->closed && !$this->isFlaggedForDespawn()) {
            if ($this->target === null) {
                $blocks = [];
                for ($x = -$this->getMinionRange(); $x <= $this->getMinionRange(); $x++) {
                    for ($z = -$this->getMinionRange(); $z <= $this->getMinionRange(); $z++) {
                        if ($x === 0 && $z === 0) continue;
                        $block = $this->level->getBlock($this->add($x, -1, $z));
                        if ($block->getId() === BlockIds::AIR || ($block->getId() === $this->getMinionInformation()->getType()->getTargetId() && $block->getDamage() === $this->getMinionInformation()->getType()->getTargetMeta())) {
                            $blocks[] = $block;
                        }
                    }
                }
                if (count($blocks) > 0) $this->target = $blocks[array_rand($blocks)];
            }
            $this->currentActionTicks++;
            if ($this->target instanceof Block) {
                $this->target = $this->level->getBlock($this->target);
                if ($this->target->getId() !== BlockIds::AIR && ($this->target->getId() !== $this->getMinionInformation()->getType()->getTargetId() || $this->target->getDamage() !== $this->getMinionInformation()->getType()->getTargetMeta())) {
                    $this->stopWorking();
                    return $hasUpdate;
                }
            }
            switch ($this->currentAction) {
                case self::ACTION_IDLE:
                    if ($this->currentActionTicks >= 60 && $this->target !== null) { //TODO: Customize
                        $this->currentAction = self::ACTION_TURNING;
                        $this->currentActionTicks = 0;
                    }
                    break;
                case self::ACTION_TURNING:
                    $this->lookAt($this->target->multiply($this->currentActionTicks / 5));
                    if ($this->currentActionTicks === 5) {
                        $this->currentAction = self::ACTION_WORKING;
                        $this->currentActionTicks = 0;
                    }
                    break;
                case self::ACTION_WORKING:
                    $isPlacing = $this->target->getId() === BlockIds::AIR;
                    if (!$isPlacing) {
                        if ($this->currentActionTicks === 1) {
                            $this->level->broadcastLevelEvent($this->target, LevelEventPacket::EVENT_BLOCK_START_BREAK, (int)(65535 / 60));
                        }
                        $pk = new AnimatePacket();
                        $pk->action = AnimatePacket::ACTION_SWING_ARM;
                        $pk->entityRuntimeId = $this->getId();
                        $this->level->broadcastPacketToViewers($this, $pk);
                    } else {
                        $this->level->broadcastLevelEvent($this->target, LevelEventPacket::EVENT_BLOCK_STOP_BREAK);
                    }
                    if ($this->currentActionTicks === 60) {
                        $this->level->addParticle(new DestroyBlockParticle($this->target->add(0.5, 0.5, 0.5), $this->target));
                        $this->level->setBlock($this->target, $this->target->getId() === BlockIds::AIR ? $this->getMinionInformation()->getType()->toBlock(): Block::get(BlockIds::AIR));
                        $drops = $this->target->getDropsForCompatibleTool(Item::get(BlockIds::AIR));
                        if (empty($drops)) $drops = $this->target->getSilkTouchDrops(Item::get(BlockIds::AIR));
                        $this->minionInventory->addItem(...$drops);
                        $this->stopWorking();
                    }
                    break;
            }
        }
        return $hasUpdate;
    }

}