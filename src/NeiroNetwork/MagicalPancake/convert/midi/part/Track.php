<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\midi\part;

class Track{

	private int $currentTick = 0;

	public function __construct(
		private string $name = "",
		private int $volume = 100,
		private int $pitchBend = 0
	){}

	public function getCurrentTick() : int{
		return $this->currentTick;
	}

	public function addCurrentTick(int $delta) : void{
		$this->currentTick += $delta;
	}

	public function getName() : string{
		return $this->name;
	}

	public function setName(string $name) : void{
		$this->name = $name;
	}

	public function getVolume() : int{
		return $this->volume;
	}

	public function setVolume(int $volume) : void{
		$this->volume = $volume;
	}

	public function getPitchBend() : int{
		return $this->pitchBend;
	}

	public function setPitchBend(int $pitchBend) : void{
		$this->pitchBend = $pitchBend;
	}
}