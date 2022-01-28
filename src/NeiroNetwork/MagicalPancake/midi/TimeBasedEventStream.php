<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi;

use NeiroNetwork\MagicalPancake\midi\event\MidiEvent;

class TimeBasedEventStream{

	/** @var MidiEvent[] $events */
	private array $events = [];

	public function addEvent(MidiEvent $event){
		$this->events[] = $event;
	}

	public function next() : ?MidiEvent{
		$event = current($this->events);
		if($event === false) return null;
		next($this->events);
		return $event;
	}
}