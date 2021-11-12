<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\player;

use NeiroNetwork\MagicalPancake\helper\AsyncDataPacket;
use NeiroNetwork\MagicalPancake\helper\AtomicPlayers;
use NeiroNetwork\MagicalPancake\midi\MidiConverter;
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
		$stream = MidiConverter::midiToStream($this->file);
		$startTime = microtime(true);

		$endOfData = false;
		while(!$endOfData){
			$data = current($stream);
			if(microtime(true) - $startTime >= $data[0]){
				foreach($this->players as $player){
					$packets = array_map(function(array $note) use ($player) : PlaySoundPacket{
						return PlaySoundPacket::create(
							$note[0],
							$player["position"][0],
							$player["position"][1],
							$player["position"][2],
							$note[2],
							MidiConverter::noteToPitch($note[1], $note[0])
						);
					}, $data[1]);
					$this->sender->send($player["sessionId"], $packets);
				}
				if(next($stream) === false){
					$endOfData = true;
				}
			}
		}
	}
}