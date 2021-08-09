<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\entities\types;

use Mcbeany\BetterMinion\entities\MinionEntity;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;

class LumberjackMinion extends MinionEntity
{

    protected function updateTarget()
    {
        for ($x = -$this->getMinionRange(); $x <= $this->getMinionRange(); $x++) {
            for ($z = -$this->getMinionRange(); $z <= $this->getMinionRange(); $z++) {
                if ($x === 0 && $z === 0) continue;
                if ($x % 2 === 0 && $z % 2 === 0) {
                    $block = $this->level->getBlock($this->add($x, 0, $z));
                    if ($block->getId() === BlockIds::SAPLING && $block->getDamage() === $this->getMinionInformation()->getType()->toTree()->sapling->getDamage()) {
                        if (mt_rand(0, 1) === 0) {
                            $this->getMinionInformation()->getType()->toTree()->placeObject($this->level, $block->getX(), $block->getY(), $block->getZ(), new Random());
                        }
                    }
                }
            }
        }
    }

    protected function getTarget()
    {
        $blocks = [];
        for ($x = -$this->getMinionRange(); $x <= $this->getMinionRange(); $x++) {
            for ($z = -$this->getMinionRange(); $z <= $this->getMinionRange(); $z++) {
                if ($x === 0 && $z === 0) continue;
                $dirt = $this->level->getBlock($this->add($x, -1, $z));
                $block = $this->level->getBlock($this->add($x, 0, $z));
                if ($x % 2 === 0 && $z % 2 === 0) {
                    if (in_array($dirt->getId(), [BlockIds::GRASS, BlockIds::DIRT, BlockIds::FARMLAND]) && ($block->getId() === BlockIds::AIR || ($block->getId() === $this->getMinionInformation()->getType()->getTargetId() && $block->getDamage() === $this->getMinionInformation()->getType()->getTargetMeta()))) {
                        $blocks[] = $block;
                    }
                }
            }
        }
        if (count($blocks) > 0) $this->target = $blocks[array_rand($blocks)];
    }

    protected function startWorking()
    {
        if ($this->target->getId() !== BlockIds::AIR) {
            for ($y = 0; $y < 3; $y++) {
                $block = $this->level->getBlock($this->target->add(0, $y));
                if ($block->getId() !== $this->getMinionInformation()->getType()->getTargetId() || $block->getDamage() !== $this->getMinionInformation()->getType()->getTargetMeta()) {
                    $this->stopWorking();
                    break;
                }
                $this->level->addParticle(new DestroyBlockParticle($block->add(0.5, 0.5, 0.5), $block));
                $this->level->setBlock($block, Block::get(BlockIds::AIR));
                $drop = Item::get($block->getId(), $block->getDamage());
                for ($i = 1; $i <= $drop->getCount(); $i++) {
                    $thing = Item::get($drop->getId(), $drop->getDamage());
                    if ($this->getMinionInventory()->canAddItem($thing)) {
                        $this->getMinionInventory()->addItem($thing);
                        $this->getMinionInformation()->incrementResourcesCollected();
                    }
                }
            }
        } else {
            $this->level->setBlock($this->target, $this->getMinionInformation()->getType()->toTree()->sapling);
        }
    }

    protected function getTool(string $tool, bool $isNetheriteTool): Item
    {
        return $isNetheriteTool ? Item::get(746) : Item::fromString($tool . " Axe");
    }

}