<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\midi\event;

use NeiroNetwork\MagicalPancake\convert\midi\part\Track;

class NoteOn extends MidiEvent{

	private string $name;
	private int $volume;
	private int $pitch;

	public function __construct(
		Track $track,
		private int $note,
		private int $velocity
	){
		$this->name = $track->getName();
		$this->volume = $track->getVolume();
		$this->pitch = $track->getPitchBend();
	}

	public function getName() : string{
		return $this->name;
	}

	public function getVolume() : int{
		return $this->volume;
	}

	public function getNote() : int{
		return $this->note;
	}

	public function getVelocity() : int{
		return $this->velocity;
	}

	public function getPitch() : int{
		return $this->pitch;
	}
}