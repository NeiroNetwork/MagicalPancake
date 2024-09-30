<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\stream\event;

final class RestEvent extends NoteEvent{

	public function __construct(
		public readonly float $time
	){}

	/** @deprecated */
	public function getTime() : float{
		return $this->time;
	}
}
