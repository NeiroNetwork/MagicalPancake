<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\utils;

use pocketmine\scheduler\AsyncPool;
use pocketmine\Server;

class PluginAsyncPool{

	private static AsyncPool $pool;

	public static function getInstance() : AsyncPool{
		return self::$pool ?? self::$pool = new AsyncPool(
				1,
				max(-1, Server::getInstance()->getConfigGroup()->getPropertyInt("memory.async-worker-hard-limit", 256)),
				Server::getInstance()->getLoader(),
				Server::getInstance()->getLogger(),
				Server::getInstance()->getTickSleeper()
			);
	}
}