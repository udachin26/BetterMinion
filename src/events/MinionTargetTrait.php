<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\events;

trait MinionTargetTrait{

	public function __construct(
		protected mixed $target
	){
	}

	public function getTarget() : mixed{
		return $this->target;
	}

}