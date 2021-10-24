<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake;

use NeiroNetwork\MagicalPancake\player\AsyncPlayer;

class MidiPlayer{

	private static ?AsyncPlayer $player = null;

	public static function play() : bool{
		return true;
	}

	public static function stop() : bool{
		return true;
	}

	public static function pause() : bool{
		return true;
	}

	public static function resume() : bool{
		return true;
	}

	public static function isPlaying() : bool{
		return self::$player !== null;
	}

	public static function isPausing() : bool{
		return false;
	}
}