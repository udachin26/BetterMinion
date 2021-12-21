<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use Mcbeany\BetterMinion\commands\MinionCommand;
use Mcbeany\BetterMinion\entities\types\FarmingMinion;
use Mcbeany\BetterMinion\entities\types\MiningMinion;
use Mcbeany\BetterMinion\minions\MinionInfo;
use Mcbeany\BetterMinion\minions\MinionNBT;
use Mcbeany\BetterMinion\minions\MinionType;
use Mcbeany\BetterMinion\minions\MinionUpgrade;
use Mcbeany\BetterMinion\utils\Configuration;
use Mcbeany\BetterMinion\utils\Language;
use pocketmine\block\BlockIdentifier;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

final class BetterMinion extends PluginBase{
	use SingletonTrait;

	const MINION_CLASSES = [
		MiningMinion::class,
		FarmingMinion::class
	];

	protected function onLoad() : void{
		self::setInstance($this);
		Configuration::load();
		Language::load();
	}

	protected function onEnable() : void{
		try{
			if(!PacketHooker::isRegistered()){
				PacketHooker::register($this);
			}
		}catch(HookAlreadyRegistered){
			//NOOP
		}
		foreach(self::MINION_CLASSES as $class){
			EntityFactory::getInstance()->register($class,
				function(World $world, CompoundTag $nbt) use ($class) : Entity{
					return new $class(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
				}, [basename($class)]
			);
		}
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
		$this->getServer()->getCommandMap()->register("minion", new MinionCommand($this, "minion", "BetterMinion Commands"));
	}

	public function createSpawner(MinionType $type, BlockIdentifier $target) : Item{
		$info = new MinionInfo(
			$type,
			$target,
			new MinionUpgrade()
		// TODO: Spawner's custom options such as level and upgrade selection
		);
		$item = Configuration::minion_spawner();
		$item->getNamedTag()->setTag(MinionNBT::INFO, $info->nbtSerialize());
		$item->setCustomName(Language::minion_spawner_name($info));
		$item->setLore(Language::minion_spawner_lore($info));
		return $item;
	}

}
