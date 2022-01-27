<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\scheduler\AsyncPool;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class SingleAsyncPool{

	private static AsyncPool $asyncPool;

	private static function getPool() : AsyncPool{
		return self::$asyncPool ?? self::$asyncPool = new AsyncPool(1,
				max(-1, Server::getInstance()->getConfigGroup()->getPropertyInt("memory.async-worker-hard-limit", 256)),
				Server::getInstance()->getLoader(),
				Server::getInstance()->getLogger(),
				Server::getInstance()->getTickSleeper()
			);
	}

	public static function submitTask(AsyncTask $task) : int{
		return self::getPool()->submitTask($task);
	}
}