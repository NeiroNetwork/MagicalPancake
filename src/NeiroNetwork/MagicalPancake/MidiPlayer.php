<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake;

use NeiroNetwork\MagicalPancake\musician\AsyncMidiMusician;
use NeiroNetwork\MagicalPancake\utils\PluginAsyncPool;

class MidiPlayer{

	private static ?AsyncMidiMusician $musician = null;

	public static function play(string $file) : bool{
		if(!self::isPlaying() && file_exists($file)){
			PluginAsyncPool::getInstance()->submitTask(self::$musician = new AsyncMidiMusician($file));
		}
		return !self::isPlaying();
	}

	public static function stop() : bool{
		self::$musician?->cancelRun();
		self::$musician = null;
		return self::isPlaying();
	}

	public static function isPlaying() : bool{
		return self::$musician?->isFinished() === false;
	}

	/*
	public static function pause() : bool{
		self::$musician?->setPause(true);
		return !self::isPaused();
	}

	public static function resume() : bool{
		self::$musician?->setPause(false);
		return self::isPaused();
	}

	public static function isPaused() : bool{
		return self::$musician?->getPause() ?? false;
	}
	*/
}