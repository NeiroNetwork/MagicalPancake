<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake;

use NeiroNetwork\MagicalPancake\helper\AtomicPlayers;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{

	protected function onEnable() : void{
		AtomicPlayers::init($this);
	}

	protected function onDisable() : void{
		MidiPlayer::stop();
	}
}