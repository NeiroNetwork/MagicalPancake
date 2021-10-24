<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\player;

class PlayerInterface{

	private static ?AsyncPlayer $player = null;

	public static function play(string $file = "") : bool{
		if(self::isPlaying()){
			return false;
		}
		return true;
	}

	public static function stop() : bool{
		self::$player?->shutdown();
		self::$player = null;
		return self::isPlaying();
	}

	public static function pause() : bool{
		self::$player?->setPause(true);
		return !self::isPaused();
	}

	public static function resume() : bool{
		self::$player?->setPause(false);
		return self::isPaused();
	}

	public static function isPlaying() : bool{
		return self::$player !== null;
	}

	public static function isPaused() : bool{
		return self::$player?->getPause() ?? false;
	}
}