<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake;

use NeiroNetwork\MagicalPancake\helper\AsyncPoolGenerator;
use NeiroNetwork\MagicalPancake\player\AsyncPlayer;
use pocketmine\scheduler\AsyncPool;

class PlayerInterface{

	private static AsyncPool $asyncPool;
	private static ?AsyncPlayer $player = null;

	public static function play(string $file = "") : bool{
		if(self::isPlaying()){
			return false;
		}

		$pool = self::$asyncPool ?? self::$asyncPool = AsyncPoolGenerator::gen();
		$pool->submitTask(self::$player = new AsyncPlayer());
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