<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\midi\event;

class SetTempo extends MidiEvent{

	public function __construct(
		private int $secondsPerQuarterNote
	){}

	public function getSecondsPerQuarterNote() : float{
		return $this->secondsPerQuarterNote / 1000000;
	}
}