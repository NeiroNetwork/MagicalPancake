<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi;

use NeiroNetwork\MagicalPancake\midi\event\TickBaseMidiEvent;

class TickBasedEvents{

	/** @var TickBaseMidiEvent[] $events */
	private array $events = [];

	public function getEvents() : array{
		return $this->events;
	}

	public function addEvent(int $tick, TickBaseMidiEvent $event) : void{
		$this->events[$tick][] = $event;
	}

	public function normalize() : void{
		ksort($this->events);
	}
}