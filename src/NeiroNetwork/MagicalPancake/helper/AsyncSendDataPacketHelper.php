<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializerContext;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\network\mcpe\raklib\RakLibPacketSender;
use pocketmine\Server;
use raklib\server\ipc\UserToRakLibThreadMessageSender;

class AsyncSendDataPacketHelper implements Listener{

	private static self $instance;

	public static function getInstance() : self{
		return self::$instance ?? self::$instance = new self();
	}

	private PacketSerializerContext $packetSerializerContext;
	private ZlibCompressor $compressor;
	private UserToRakLibThreadMessageSender $interface;

	private \Volatile $players;

	public function __construct(){
		$this->players = new \Volatile();

		$this->packetSerializerContext = new PacketSerializerContext(GlobalItemTypeDictionary::getInstance()->getDictionary());
		$this->compressor = new ZlibCompressor(1, ZlibCompressor::DEFAULT_THRESHOLD, ZlibCompressor::DEFAULT_MAX_DECOMPRESSION_SIZE);

		// grab RakLibInterface::$interface
		foreach(Server::getInstance()->getNetwork()->getInterfaces() as $interface){
			if($interface instanceof RakLibInterface){
				$interface = (new \ReflectionClass($interface))->getProperty("interface");
				$interface->setAccessible(true);
				$this->interface = $interface->getValue($interface);
				break;
			}
		}
	}

	public function onJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$session = $player->getNetworkSession();

		// grab NetworkSession::$sender
		/* cipherを取得しても非同期で動作しない
		$cipher = (new \ReflectionClass($session))->getProperty("cipher");
		$cipher->setAccessible(true);
		$this->cipher = $cipher->getValue($session);
		*/

		// grab NetworkSession::$sender
		$sender = (new \ReflectionClass($session))->getProperty("sender");
		$sender->setAccessible(true);
		$sender = $sender->getValue($session);
		/** @var RakLibPacketSender $sender */
		// grab RakLibPacketSender::$sessionId
		$sessionId = (new \ReflectionClass($sender))->getProperty("sessionId");
		$sessionId->setAccessible(true);
		$sessionId = $sessionId->getValue($sender);
		/** @var int $sessionId */

		$this->players[$player->getName()]["sessionId"] = $sessionId;
	}

	public function onQuit(PlayerQuitEvent $event) : void{
		unset($this->players[$event->getPlayer()->getName()]);
	}

	public function onMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();
		$pos = $event->getTo();
		$this->players[$player->getName()]["x"] = $pos->x;
		$this->players[$player->getName()]["y"] = $pos->y;
		$this->players[$player->getName()]["z"] = $pos->z;
	}
}