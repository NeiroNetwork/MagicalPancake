<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi\event;

class Rest extends TimeBaseMidiEvent{

	public function __construct(
		private float $restTime
	){}

	public function getRestTime() : float{
		return $this->restTime;
	}
}