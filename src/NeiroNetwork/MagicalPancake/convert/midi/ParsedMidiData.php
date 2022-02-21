<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\midi;

use NeiroNetwork\MagicalPancake\convert\midi\event\MidiEvent;

class ParsedMidiData{

	private int $ticksPerBeat;

	/** @var MidiEvent[][] */
	private array $events = [];

	public function getTicksPerBeat() : int{
		return $this->ticksPerBeat;
	}

	public function setTicksPerBeat(int $ticksPerBeat) : void{
		$this->ticksPerBeat = $ticksPerBeat;
	}

	public function getEvents() : array{
		return $this->events;
	}

	public function addEvent(int $tick, MidiEvent $event) : void{
		$this->events[$tick][] = $event;
	}

	public function finalize() : void{
		assert($this->ticksPerBeat > 0);
		ksort($this->events);
	}
}