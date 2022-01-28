<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\scheduler\Task;

class PlayerUpdater extends Task implements Listener{

	public function __construct(
		private AtomicPlayers $players,
	){}

	public function onJoin(PlayerJoinEvent $event) : void{
		$this->players->addPlayer($event->getPlayer());
	}

	public function onQuit(PlayerQuitEvent $event) : void{
		$this->players->removePlayer($event->getPlayer());
	}

	public function onMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();
		$this->players->getPlayer($player)->setPosition($event->getTo()->add(0, $player->getEyeHeight(), 0));
	}

	public function onRun() : void{
		foreach(MusicianStore::getInstance()->getMusicians() as $musician){
			$musician->updatePlayers($this->players);
		}
	}
}