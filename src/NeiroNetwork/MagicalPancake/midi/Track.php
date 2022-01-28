<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi;

class Track{

	private int $tick = 0;
	private string $name = "";
	private int $volume = 100;

	public function getTick() : int{
		return $this->tick;
	}

	public function addTick(int $delta) : void{
		$this->tick += $delta;
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
}