<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi\event;

class SetTempo extends TickBaseMidiEvent{

	public function __construct(
		private int $secondsPerQuarterNote
	){}

	public function getSecondsPerQuarterNote() : float{
		return $this->secondsPerQuarterNote / 1000000;
	}
}