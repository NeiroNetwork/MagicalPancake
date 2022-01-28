<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi\event;

class NoteOn extends TickBaseMidiEvent{

	public function __construct(
		private int $note,
		private string $sound,
		private int $velocity,
		private int $volume,
	){}

	public function getNote() : int{
		return $this->note;
	}

	public function getSound() : string{
		return $this->sound;
	}

	public function getVelocity() : int{
		return $this->velocity;
	}

	public function getVolume() : int{
		return $this->volume;
	}
}