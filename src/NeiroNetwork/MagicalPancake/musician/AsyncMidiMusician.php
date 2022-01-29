<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\musician;

use NeiroNetwork\MagicalPancake\helper\AtomicPlayers;
use NeiroNetwork\MagicalPancake\helper\MusicianStore;
use NeiroNetwork\MagicalPancake\midi\event\NoteOn;
use NeiroNetwork\MagicalPancake\midi\event\NotesOn;
use NeiroNetwork\MagicalPancake\midi\event\Rest;
use NeiroNetwork\MagicalPancake\midi\NoteConverter;
use NeiroNetwork\MagicalPancake\midi\MidiFileConverter;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\scheduler\AsyncTask;

class AsyncMidiMusician extends AsyncTask{

	private AtomicPlayers $players;

	public function __construct(
		private string $file
	){
		MusicianStore::getInstance()->addMusician($this);
		$this->players = AtomicPlayers::getInstance();
	}

	public function updatePlayers(AtomicPlayers $players) : void{
		$this->players = $players;
	}

	public function onRun() : void{
		$stream = MidiFileConverter::convert($this->file);

		$startTime = microtime(true);
		while(!$this->hasCancelledRun() && $event = $stream->next()){
			if($event instanceof NotesOn){
				foreach($this->players->getPlayers() as $player){
					$packets = array_map(fn(NoteOn $note) => PlaySoundPacket::create(
						$note->getSound(),
						$player->x,
						$player->y,
						$player->z,
						NoteConverter::toVolume($note),
						NoteConverter::toPitch($note)
					), $event->getNotes());
					$player->sendDataPackets($packets);
				}
			}elseif($event instanceof Rest){
				try{
					time_sleep_until($startTime += $event->getRestTime());
				}catch(\ErrorException){
					// なぜかわからないが E_WARNING が発生する
				}
			}
		}
	}

	public function onCompletion() : void{
		MusicianStore::getInstance()->removeMusician($this);
	}
}