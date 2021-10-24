<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\player;

use pocketmine\scheduler\AsyncTask;

class AsyncPlayer extends AsyncTask{

	private bool $pause = false;
	private bool $shutdown = false;

	public function getPause() : bool{
		return $this->pause;
	}

	public function setPause(bool $pause = true) : void{
		$this->pause = $pause;
	}

	public function shutdown() : void{
		$this->shutdown = true;
	}

	public function onRun() : void{
		// TODO: Implement onRun() method.
	}
}