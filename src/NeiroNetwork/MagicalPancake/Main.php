<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake;

use NeiroNetwork\MagicalPancake\helper\AsyncDataPacket;
use NeiroNetwork\MagicalPancake\helper\EventListener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Main extends PluginBase{

	protected function onEnable() : void{
		$this->getScheduler()->scheduleTask(new ClosureTask(\Closure::fromCallable([AsyncDataPacket::class, "initOnMain"])));
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}
}