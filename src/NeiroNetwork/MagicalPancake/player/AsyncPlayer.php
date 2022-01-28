<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\player;

use NeiroNetwork\MagicalPancake\helper\AsyncDataPacket;
use NeiroNetwork\MagicalPancake\helper\AtomicPlayers;
use NeiroNetwork\MagicalPancake\midi\event\NotesOn;
use NeiroNetwork\MagicalPancake\midi\event\Rest;
use NeiroNetwork\MagicalPancake\midi\MidiConverter;
use NeiroNetwork\MagicalPancake\midi\MidiFileConverter;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\scheduler\AsyncTask;

class AsyncPlayer extends AsyncTask{

	public AtomicPlayers $players;
	private AsyncDataPacket $sender;

	public function __construct(private string $file){
		$this->players = AtomicPlayers::getInstance();
		$this->sender = AsyncDataPacket::getInstance();
	}

	public function onRun() : void{
		$startTime = microtime(true);
		$stream = MidiFileConverter::convert($this->file);
		while($event = $stream->next()){
			if($event instanceof NotesOn){
				foreach($this->players as $player){
					$packets = array_map(fn($note) => PlaySoundPacket::create(
						$note->getSound(),
						$player["position"][0],
						$player["position"][1],
						$player["position"][2],
						($note->getVolume() / 100) * ($note->getVelocity() / 100),
						MidiConverter::noteToPitch($note->getNote(), $note->getSound())
					), $event->getNotes());
					$this->sender->send($player["sessionId"], $packets);
				}
			}elseif($event instanceof Rest){
				time_sleep_until($startTime += $event->getRestTime());
			}
		}
	}
}