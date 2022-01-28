<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi\event;

class SetTempo extends MidiEvent{

	public function __construct(
		private int $microsecondsPerQuarterNote
	){}

	public function getMicrosecondsPerQuarterNote() : int{
		return $this->microsecondsPerQuarterNote;
	}
}