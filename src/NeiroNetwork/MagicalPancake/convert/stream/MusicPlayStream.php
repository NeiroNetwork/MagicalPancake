<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\stream;

use NeiroNetwork\MagicalPancake\convert\stream\event\NoteEvent;

class MusicPlayStream{

	/** @var NoteEvent[] */
	private array $events = [];

	public function push(NoteEvent $event) : void{
		$this->events[] = $event;
	}

	public function next() : ?NoteEvent{
		$event = current($this->events);
		if($event === false) return null;
		next($this->events);
		return $event;
	}

	public function reset() : ?NoteEvent{
		$event = reset($this->events);
		return $event === false ? null : $event;
	}
}