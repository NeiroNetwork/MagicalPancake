<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializerContext;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\Server;
use raklib\server\ipc\UserToRakLibThreadMessageSender;

class GlobalNecessaryInstances{

	private static PacketSerializerContext $packetSerializer;
	private static ZlibCompressor $zlibCompressor;
	private static UserToRakLibThreadMessageSender $messageSender;

	public static function getPacketSerializer() : PacketSerializerContext{
		return self::$packetSerializer ?? self::$packetSerializer =
				new PacketSerializerContext(GlobalItemTypeDictionary::getInstance()->getDictionary());
	}

	public static function getZlibCompressor() : ZlibCompressor{
		return self::$zlibCompressor ?? self::$zlibCompressor =
				new ZlibCompressor(1, ZlibCompressor::DEFAULT_THRESHOLD, ZlibCompressor::DEFAULT_MAX_DECOMPRESSION_SIZE);
	}

	public static function getMessageSender() : UserToRakLibThreadMessageSender{
		if(!isset(self::$messageSender)){
			foreach(Server::getInstance()->getNetwork()->getInterfaces() as $interface){
				if($interface instanceof RakLibInterface){
					$interface = (new \ReflectionClass($interface))->getProperty("interface");
					$interface->setAccessible(true);
					self::$messageSender = $interface->getValue($interface);
					break;
				}
			}
		}

		return self::$messageSender;
	}
}