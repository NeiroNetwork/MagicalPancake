<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake;

use NeiroNetwork\MagicalPancake\helper\AtomicPlayers;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents(AtomicPlayers::getInstance(), $this);
	}
}