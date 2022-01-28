<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class AtomicPlayers{

	private static self $instance;

	public static function init(Plugin $plugin) : void{
		self::$instance = new self($plugin);
	}

	public static function getInstance() : self{
		return self::$instance ?? throw new \RuntimeException("AtomicPlayers must be initialized");
	}

	/** @var SerializablePlayer[] */
	private array $players = [];

	public function __construct(Plugin $plugin){
		$updater = new PlayerUpdater($this);
		$plugin->getServer()->getPluginManager()->registerEvents($updater, $plugin);
		$plugin->getScheduler()->scheduleRepeatingTask($updater, 1);
	}

	public function getPlayers() : array{
		return $this->players;
	}

	public function addPlayer(Player $player) : void{
		$session = $player->getNetworkSession();
		$property = (new \ReflectionClass($session))->getProperty("sender");
		$property->setAccessible(true);
		$sender = $property->getValue($session);
		$property = (new \ReflectionClass($sender))->getProperty("sessionId");
		$property->setAccessible(true);
		$sessionId = $property->getValue($sender);

		$this->players[$player->getId()] = new SerializablePlayer($sessionId, $player->getEyePos());
	}

	public function removePlayer(Player $player) : void{
		unset($this->players[$player->getId()]);
	}

	public function getPlayer(Player $player) : ?SerializablePlayer{
		return $this->players[$player->getId()] ?? null;
	}
}