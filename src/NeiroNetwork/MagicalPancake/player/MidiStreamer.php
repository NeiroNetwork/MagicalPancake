<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\player;

use NeiroNetwork\MagicalPancake\convert\stream\event\PlayNotesEvent;
use NeiroNetwork\MagicalPancake\convert\stream\event\RestEvent;
use NeiroNetwork\MagicalPancake\convert\stream\MusicPlayStream;
use NeiroNetwork\MagicalPancake\convert\stream\part\MinecraftNote;
use pmmp\thread\ThreadSafeArray;
use pocketmine\snooze\SleeperHandlerEntry;
use pocketmine\thread\Thread;

class MidiStreamer extends Thread{

	/** @var ThreadSafeArray<MinecraftNote> */
	public ThreadSafeArray $notes;

	public function __construct(
		private readonly MusicPlayStream $stream,
		private readonly SleeperHandlerEntry $entry,
	){
		$this->notes = new ThreadSafeArray();
	}

	public function onRun() : void{
		$notifier = $this->entry->createNotifier();

		$startTime = microtime(true);
		while(!$this->isKilled && $event = $this->stream->currentNext()){
			if($event instanceof PlayNotesEvent){
				foreach($event->getNotes() as $note){
					$this->notes[] = $note;
				}
				$notifier->wakeupSleeper();
			}elseif($event instanceof RestEvent){
				// なぜかわからないが E_WARNING が発生する
				@time_sleep_until($startTime += $event->getTime());
			}
		}
	}
}
