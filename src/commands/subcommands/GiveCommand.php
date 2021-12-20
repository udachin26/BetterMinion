<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Mcbeany\BetterMinion\commands\arguments\TypeArgument;
use pocketmine\command\CommandSender;

class GiveCommand extends BaseSubCommand{

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$sender->sendMessage("hi");
	}

	/**
	 * @throws ArgumentOrderException
	 */
	protected function prepare() : void{
		$this->registerArgument(0, new TypeArgument());
	}
}