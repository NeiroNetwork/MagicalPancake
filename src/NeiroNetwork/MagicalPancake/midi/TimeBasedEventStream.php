<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi;

use NeiroNetwork\MagicalPancake\midi\event\TimeBaseMidiEvent;

class TimeBasedEventStream{

	/** @var TimeBaseMidiEvent[] $events */
	private array $events = [];

	public function addEvent(TimeBaseMidiEvent $event){
		$this->events[] = $event;
	}

	public function next() : ?TimeBaseMidiEvent{
		$event = current($this->events);
		if($event === false) return null;
		next($this->events);
		return $event;
	}
}