<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\scheduler\AsyncPool;
use pocketmine\Server;

class AsyncPoolGenerator{

	public static function gen() : AsyncPool{
		$server = Server::getInstance();
		$limit = max(-1, $server->getConfigGroup()->getPropertyInt("memory.async-worker-hard-limit", 256));
		return new AsyncPool(1, $limit, $server->getLoader(), $server->getLogger(), $server->getTickSleeper());
	}
}