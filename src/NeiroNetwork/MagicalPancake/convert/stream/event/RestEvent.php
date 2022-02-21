<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\stream\event;

class RestEvent extends NoteEvent{

	public function __construct(
		private float $time
	){}

	public function getTime() : float{
		return $this->time;
	}
}