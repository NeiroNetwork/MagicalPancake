<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake;

use NeiroNetwork\MagicalPancake\helper\SingleAsyncPool;
use NeiroNetwork\MagicalPancake\player\AsyncPlayer;

class PlayerInterface{

	private static ?AsyncPlayer $player = null;

	public static function play(string $file = "") : bool{
		if(!self::isPlaying()){
			SingleAsyncPool::submitTask(self::$player = new AsyncPlayer());
		}
		return !self::isPlaying();
	}

	public static function stop() : bool{
		self::$player?->shutdown();
		self::$player = null;
		return self::isPlaying();
	}

	public static function isPlaying() : bool{
		return self::$player !== null;
	}

	/*
	public static function pause() : bool{
		self::$player?->setPause(true);
		return !self::isPaused();
	}

	public static function resume() : bool{
		self::$player?->setPause(false);
		return self::isPaused();
	}

	public static function isPaused() : bool{
		return self::$player?->getPause() ?? false;
	}
	*/
}