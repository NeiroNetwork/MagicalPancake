<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializerContext;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\Server;
use raklib\server\ipc\UserToRakLibThreadMessageSender;

class AsyncSendDataPacketHelper{

	private static self $instance;

	public static function getInstance() : self{
		return self::$instance ?? self::$instance = new self();
	}

	private PacketSerializerContext $packetSerializerContext;
	private ZlibCompressor $compressor;
	private UserToRakLibThreadMessageSender $interface;

	public function __construct(){
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
}