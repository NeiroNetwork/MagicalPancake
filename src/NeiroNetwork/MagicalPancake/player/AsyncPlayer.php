<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\player;

use NeiroNetwork\MagicalPancake\helper\AtomicPlayers;
use NeiroNetwork\MagicalPancake\midi\MidiConverter;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\scheduler\AsyncTask;

class AsyncPlayer extends AsyncTask{

	private AtomicPlayers $players;

	public function __construct(private string $file){
		$this->players = AtomicPlayers::getInstance();
	}

	public function onRun() : void{
		$stream = MidiConverter::midiToStream($this->file);
		$startTime = microtime(true);

		$endOfData = false;
		while(!$endOfData){
			$data = current($stream);
			if(microtime(true) - $startTime >= $data[0]){
				foreach($this->players->getAll() as $player){
					$packets = array_map(function(array $note) use ($player) : PlaySoundPacket{
						return PlaySoundPacket::create(
							$note[0],
							$player->position->x,
							$player->position->y,
							$player->position->z,
							$note[2],
							MidiConverter::noteToPitch($note[1], $note[0])
						);
					}, $data[1]);
					$player->sendDataPacket($packets);
				}
				if(next($stream) === false){
					$endOfData = true;
				}
			}
		}
	}
}