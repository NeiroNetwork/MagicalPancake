<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\stream;

use NeiroNetwork\MagicalPancake\convert\stream\event\NoteEvent;
use pmmp\thread\ThreadSafe;
use pmmp\thread\ThreadSafeArray;

final class MusicPlayStream extends ThreadSafe{

	/** @var ThreadSafeArray<int, NoteEvent> */
	private ThreadSafeArray $events;

	private int $pointer = 0;

	public function __construct(){
		$this->events = new ThreadSafeArray();
	}

	public function push(NoteEvent $event) : void{
		$this->events[] = $event;
	}

	public function currentNext() : ?NoteEvent{
		$event = $this->current();
		$this->next();
		return $event;
	}

	public function current() : ?NoteEvent{
		return $this->events[$this->pointer];
	}

	public function next() : ?NoteEvent{
		if($this->current() !== null){
			$this->pointer++;
		}
		return $this->current();
	}

	public function prev() : ?NoteEvent{
		if($this->current() !== null){
			$this->pointer--;
		}
		return $this->current();
	}

	public function rewind() : ?NoteEvent{
		return $this->events[$this->pointer = 0];
	}
}
