<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\player;

use NeiroNetwork\MagicalPancake\helper\AtomicPlayers;
use NeiroNetwork\MagicalPancake\midi\MidiConverter;
use pocketmine\scheduler\AsyncTask;

class AsyncPlayer extends AsyncTask{

	private AtomicPlayers $players;

	private bool $endMidi = false;

	public function __construct(private string $file){
		$this->players = AtomicPlayers::getInstance();
	}

	public function onRun() : void{
		$stream = MidiConverter::midiToStream($this->file);
	}

	public function isEnd() : bool{
		return $this->endMidi;
	}
}