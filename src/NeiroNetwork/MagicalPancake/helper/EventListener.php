<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventListener implements Listener{

	private AtomicPlayers $players;

	public function __construct(){
		$this->players = AtomicPlayers::getInstance();
	}

	public function onJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$name = $player->getName();
		$position = $player->getPosition();

		$session = $player->getNetworkSession();
		$property = (new \ReflectionClass($session))->getProperty("sender");
		$property->setAccessible(true);
		$sender = $property->getValue($session);
		$property = (new \ReflectionClass($sender))->getProperty("sessionId");
		$property->setAccessible(true);
		$this->players[$name]["sessionId"] = $property->getValue($sender);

		$this->players[$name]["position"] = [$position->x, $position->y, $position->z];
	}

	public function onQuit(PlayerQuitEvent $event) : void{
		unset($this->players[$event->getPlayer()->getName()]);
	}

	public function onMove(PlayerMoveEvent $event) : void{
		$to = $event->getTo();
		$this->players[$event->getPlayer()->getName()]["position"] = [$to->x, $to->y, $to->z];
	}
}