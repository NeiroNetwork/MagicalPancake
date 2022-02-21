<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\stream\part;

class MinecraftNote{

	public function __construct(
		private string $sound,
		private float $volume,
		private float $pitch
	){}

	public function getSound() : string{
		return $this->sound;
	}

	public function getVolume() : float{
		return $this->volume;
	}

	public function getPitch() : float{
		return $this->pitch;
	}
}