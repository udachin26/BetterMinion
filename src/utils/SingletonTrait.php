<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use Mcbeany\BetterMinion\BetterMinion;

// SingletonTrait :D
// Why? Because I hate PocketMine's default singleton implementation.
// Easy to use & understand.
trait SingletonTrait{
	/** @var static */
	private static $instance;

	final public function __construct(
		private BetterMinion $plugin
	){}

	public static function init(BetterMinion $plugin) : void{
		(self::$instance = new static($plugin))->onInit();
	}

	protected function onInit() : void{}

	public static function getInstance() : static{
		return self::$instance;
	}

	protected function getPlugin() : BetterMinion{
		return $this->plugin;
	}
}
