<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\math\Vector3;
use pocketmine\player\Player;

class MinimumPlayer{

	public int $sessionId;
	public Vector3 $position;

	public function __construct(Player $player){
		$session = $player->getNetworkSession();
		$property = (new \ReflectionClass($session))->getProperty("sender");
		$property->setAccessible(true);
		$sender = $property->getValue($session);
		$property = (new \ReflectionClass($sender))->getProperty("sessionId");
		$property->setAccessible(true);
		$this->sessionId = $property->getValue($sender);

		$this->position = $player->getLocation()->asVector3();
	}
}