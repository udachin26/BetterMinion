<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use AndreasHGK\SellAll\SellAll;
use Mcbeany\BetterMinion\commands\MinionCommand;
use Mcbeany\BetterMinion\entities\objects\Farmland;
use Mcbeany\BetterMinion\entities\types\FarmingMinion;
use Mcbeany\BetterMinion\entities\types\LumberjackMinion;
use Mcbeany\BetterMinion\entities\types\MiningMinion;
use muqsit\invmenu\InvMenu;
use muqsit\invcrashfix\Loader as InvCrashFix;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\block\BlockFactory;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class BetterMinion extends PluginBase
{
    use SingletonTrait;

    /** @var string[] */
    public static $minions = [MiningMinion::class, FarmingMinion::class, LumberjackMinion::class];

    public function onLoad()
    {
        $this->saveDefaultConfig();
        self::setInstance($this);
    }

    public function onEnable()
    {
        foreach (self::$minions as $minion) {
            Entity::registerEntity($minion, true);
        }
        BlockFactory::registerBlock(new Farmland(), true);
        $this->getServer()->getCommandMap()->register("minion", new MinionCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        foreach ([InvMenu::class, SellAll::class] as $class) {
            if (!class_exists($class)) {
                $this->getLogger()->alert("$class not found! Please download this plugin from Poggit CI. Disabling plugin...");
                $this->getServer()->getPluginManager()->disablePlugin($this);
            }
        }
        if (!class_exists(InvCrashFix::class)) {
            $this->getLogger()->notice("InvCrashFix is required to fix client crashes on 1.16+, download it here: https://poggit.pmmp.io/ci/Muqsit/InvCrashFix");
        }
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
    }

}
