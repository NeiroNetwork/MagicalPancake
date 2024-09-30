<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\player;

use NeiroNetwork\MagicalPancake\convert\MidiFileConverter;
use NeiroNetwork\MagicalPancake\convert\stream\MusicPlayStream;
use NeiroNetwork\MagicalPancake\convert\stream\part\MinecraftNote;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class MidiPlayer{

	/** @var Player[] */
	protected array $recipients = [];

	protected MusicPlayStream $stream;

	protected ?MidiStreamer $thread = null;

	public function __construct(){}

	public function addRecipient(Player $player) : void{
		$this->recipients[$player->getId()] = $player;
	}

	public function removeRecipient(Player $player) : void{
		unset($this->recipients[$player->getId()]);
	}

	public function load(string $filename) : void{
		$this->stream = MidiFileConverter::convert($filename);
	}

	public function play() : void{
		$entry = Server::getInstance()->getTickSleeper()->addNotifier(function(){
			/** @var MinecraftNote[] $notes */
			$notes = $this->thread->notes->chunk($this->thread->notes->count());
			foreach($this->recipients as $player){
				$position = $player->getPosition();
				foreach($notes as $note){
					$pk = PlaySoundPacket::create($note->sound, $position->x, $position->y, $position->z, $note->volume, $note->pitch);
					$player->getNetworkSession()->sendDataPacket($pk, true);
				}
			}
		});
		$this->thread = new MidiStreamer($this->stream, $entry);
		$this->thread->start();
	}

	public function pause() : void{
		$this->thread?->quit();
	}

	public function stop() : void{
		$this->thread?->quit();
		$this->stream->rewind();
	}
}
