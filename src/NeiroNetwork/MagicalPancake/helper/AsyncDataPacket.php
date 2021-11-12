<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\network\mcpe\compression\CompressBatchPromise;
use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\serializer\PacketBatch;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializerContext;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\Server;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\PacketReliability;
use raklib\server\ipc\UserToRakLibThreadMessageSender;

class AsyncDataPacket{

	private static self $instance;

	public static function getInstance() : self{
		return self::$instance;
	}

	public static function initOnMain() : void{
		self::$instance = new self();
	}

	private PacketSerializerContext $packetSerializer;
	private ZlibCompressor $zlibCompressor;
	private UserToRakLibThreadMessageSender $messageSender;

	private function __construct(){
		$this->packetSerializer = new PacketSerializerContext(GlobalItemTypeDictionary::getInstance()->getDictionary());
		$this->zlibCompressor = new ZlibCompressor(1, ZlibCompressor::DEFAULT_THRESHOLD, ZlibCompressor::DEFAULT_MAX_DECOMPRESSION_SIZE);
		foreach(Server::getInstance()->getNetwork()->getInterfaces() as $interface){
			if($interface instanceof RakLibInterface){
				$property = (new \ReflectionClass($interface))->getProperty("interface");
				$property->setAccessible(true);
				$this->messageSender = $property->getValue($interface);
				break;
			}
		}
	}

	public function send(int $sessionId, DataPacket|array $packet, bool $immediate = false) : void{
		// NetworkSession::sendDataPacket()
		// NetworkSession::addToSendBuffer()
		$sendBuffer = is_array($packet) ? $packet : [$packet];
		// NetworkSession::flushSendBuffer()
		$stream = PacketBatch::fromPackets($this->packetSerializer, ...$sendBuffer);
		// Server::prepareBatch()
		$buffer = $stream->getBuffer();
		$promise = new CompressBatchPromise();
		$promise->resolve($this->zlibCompressor->compress($buffer));
		// NetworkSession::queueCompressedNoBufferFlush()
		// NetworkSession::sendEncoded()
		$payload = $promise->getResult();
		// NetworkSession::sendEncoded()
		/* Encryption here */
		// RakLibPacketSender::send()
		// RakLibInterface::putPacket()
		$pk = new EncapsulatedPacket();
		$pk->buffer = "\xfe" . $payload;	// RakLibInterface::MCPE_RAKNET_PACKET_ID = "\xfe"
		$pk->reliability = PacketReliability::RELIABLE_ORDERED;
		$pk->orderChannel = 0;

		$this->messageSender->sendEncapsulated($sessionId, $pk, $immediate);
	}
}