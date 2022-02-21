<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\musician;

use NeiroNetwork\MagicalPancake\convert\MidiFileConverter;
use NeiroNetwork\MagicalPancake\convert\stream\event\PlayNotesEvent;
use NeiroNetwork\MagicalPancake\convert\stream\event\RestEvent;
use NeiroNetwork\MagicalPancake\convert\stream\part\MinecraftNote;
use NeiroNetwork\MagicalPancake\helper\AtomicPlayers;
use NeiroNetwork\MagicalPancake\helper\MusicianStore;
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
			if($event instanceof PlayNotesEvent){
				foreach($this->players->getPlayers() as $player){
					$packets = array_map(fn(MinecraftNote $note) => PlaySoundPacket::create(
						$note->getSound(),
						$player->x,
						$player->y,
						$player->z,
						$note->getVolume(),
						$note->getPitch()
					), $event->getNotes());
					$player->sendDataPackets($packets);
				}
			}elseif($event instanceof RestEvent){
				// なぜかわからないが E_WARNING が発生する
				@time_sleep_until($startTime += $event->getTime());
			}
		}
	}

	public function onCompletion() : void{
		MusicianStore::getInstance()->removeMusician($this);
	}
}