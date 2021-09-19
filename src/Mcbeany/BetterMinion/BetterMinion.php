<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use CortexPE\Commando\PacketHooker as Commando;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invcrashfix\Loader as InvCrashFix;
use JackMD\ConfigUpdater\ConfigUpdater;
use Mcbeany\BetterMinion\commands\MinionCommand;
use Mcbeany\BetterMinion\entities\objects\Farmland;
use Mcbeany\BetterMinion\entities\types\FarmingMinion;
use Mcbeany\BetterMinion\entities\types\LumberjackMinion;
use Mcbeany\BetterMinion\entities\types\MiningMinion;
use pocketmine\block\BlockFactory;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class BetterMinion extends PluginBase
{
    use SingletonTrait;

    /** @var string[] */
    public static $minions = [MiningMinion::class, FarmingMinion::class, LumberjackMinion::class];

    /** @var string[] */
    public $isRemove = [];

    public function onLoad(): void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();
        $this->saveResource("smelts.json");
        $this->saveResource("compacts.json");
    }

    public function onEnable(): void
    {
        foreach ([InvMenu::class, ConfigUpdater::class, Commando::class] as $class) {
            if (!class_exists($class)) {
                $this->getLogger()->alert("$class not found! Please download this plugin from Poggit CI. Disabling plugin...");
                $this->getServer()->getPluginManager()->disablePlugin($this);
            }
        }
        if (!class_exists(InvCrashFix::class)) {
            $this->getLogger()->notice("InvCrashFix is required to fix client crashes on 1.16+, download it here: https://poggit.pmmp.io/ci/Muqsit/InvCrashFix");
        }
        foreach (self::$minions as $minion) {
            Entity::registerEntity($minion, true);
        }
        BlockFactory::registerBlock(new Farmland(), true);
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        ConfigUpdater::checkUpdate($this, $this->getConfig(), "config-version", 1);
        if (!Commando::isRegistered()) {
            Commando::register($this);
        }
        $this->getServer()->getCommandMap()->register("Minion", new MinionCommand($this, "minion", "Minion commands"));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }
}
