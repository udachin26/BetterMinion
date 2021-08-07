<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use Mcbeany\BetterMinion\commands\MinionCommand;
use Mcbeany\BetterMinion\entities\MinionEntity;
use Mcbeany\BetterMinion\entities\types\MiningMinion;
use muqsit\invmenu\InvMenu;
use muqsit\invcrashfix\Loader as InvCrashFix;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class BetterMinion extends PluginBase
{
    use SingletonTrait;

    /** @var string[] */
    private $minions = [MiningMinion::class];

    public function onLoad()
    {
        $this->saveDefaultConfig();
        self::setInstance($this);
    }

    public function onEnable()
    {
        foreach ($this->minions as $minion) {
            Entity::registerEntity($minion, true);
        }
        Entity::registerEntity(MinionEntity::class, true);
        $this->getServer()->getCommandMap()->register("minion", new MinionCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        if (!class_exists(InvMenu::class)) {
            $this->getLogger()->alert("InvMenu dependency not found! Please download this plugin from Poggit CI. Disabling plugin...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        if (!class_exists(InvCrashFix::class)) {
            $this->getLogger()->notice("InvCrashFix is required to fix client crashes on 1.16+, download it here: https://poggit.pmmp.io/ci/Muqsit/InvCrashFix");
        }
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
    }

}
