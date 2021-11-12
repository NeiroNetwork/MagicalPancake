<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

class AtomicPlayers extends \ArrayObject{

	private static self $instance;

	public static function getInstance() : self{
		return self::$instance ?? self::$instance = new self();
	}
}