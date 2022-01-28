<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi;

use NeiroNetwork\MagicalPancake\midi\event\MidiEvent;

class TickBasedEvents{

	/** @var MidiEvent[] $events */
	private array $events = [];

	public function getEvents() : array{
		return $this->events;
	}

	public function addEvent(int $tick, MidiEvent $event) : void{
		$this->events[$tick][] = $event;
	}

	public function normalize() : void{
		ksort($this->events);
	}
}