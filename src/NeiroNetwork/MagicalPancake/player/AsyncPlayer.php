<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\player;

use NeiroNetwork\MagicalPancake\helper\AtomicPlayers;
use pocketmine\scheduler\AsyncTask;

class AsyncPlayer extends AsyncTask{

	private AtomicPlayers $players;

	private bool $shutdown = false;
	private bool $pause = false;

	public function __construct(){
		$this->players = AtomicPlayers::getInstance();
	}

	public function onRun() : void{
	}

	public function shutdown() : void{
		$this->shutdown = true;
	}

	public function getPause() : bool{
		return $this->pause;
	}

	public function setPause(bool $pause = true) : void{
		$this->pause = $pause;
	}
}