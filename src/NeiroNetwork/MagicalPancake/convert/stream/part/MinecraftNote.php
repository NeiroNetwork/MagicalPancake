<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\stream\part;

use pmmp\thread\ThreadSafe;

final class MinecraftNote extends ThreadSafe{

	public function __construct(
		public readonly string $sound,
		public readonly float $volume,
		public readonly float $pitch
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
