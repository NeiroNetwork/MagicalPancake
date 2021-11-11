<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;

class AtomicPlayers extends \Threaded implements Listener{

	private static self $instance;

	public static function getInstance() : self{
		return self::$instance ?? self::$instance = new self();
	}

	/** @var MinimumPlayer[] */
	private array $players = [];

	private function __construct(){
		// NOOP
	}

	public function getAll() : array{
		return $this->players;
	}

	public function onJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$this->players[$player->getName()] = new MinimumPlayer($player);
	}

	public function onQuit(PlayerQuitEvent $event) : void{
		var_dump($this->players);
		unset($this->players[$event->getPlayer()->getName()]);
	}

	public function onMove(PlayerMoveEvent $event) : void{
		$to = $event->getTo();
		$position = $this->players[$event->getPlayer()->getName()]->position;
		$position->x = $to->x;
		$position->y = $to->y;
		$position->z = $to->z;
	}
}