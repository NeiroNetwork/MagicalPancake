<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\compression\CompressBatchPromise;
use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\serializer\PacketBatch;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializerContext;
use pocketmine\player\Player;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\PacketReliability;
use raklib\server\ipc\UserToRakLibThreadMessageSender;

class AsyncablePlayer{

	public int $sessionId;
	public Vector3 $position;

	private PacketSerializerContext $packetSerializer;
	private ZlibCompressor $zlibCompressor;
	private UserToRakLibThreadMessageSender $messageSender;

	public function __construct(Player $player){
		$session = $player->getNetworkSession();
		$property = (new \ReflectionClass($session))->getProperty("sender");
		$property->setAccessible(true);
		$sender = $property->getValue($session);
		$property = (new \ReflectionClass($sender))->getProperty("sessionId");
		$property->setAccessible(true);
		$this->sessionId = $property->getValue($sender);

		$this->position = $player->getLocation()->asVector3();

		$this->packetSerializer = GlobalNecessaryInstances::getPacketSerializer();
		$this->zlibCompressor = GlobalNecessaryInstances::getZlibCompressor();
		$this->messageSender = GlobalNecessaryInstances::getMessageSender();
	}

	public function sendDataPacket(DataPacket|array $packet, bool $immediate = false) : void{
		// NetworkSession::sendDataPacket()
		// NetworkSession::addToSendBuffer()
		$sendBuffer[] = is_array($packet) ? $packet : [$packet];
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

		$this->messageSender->sendEncapsulated($this->sessionId, $pk, $immediate);
	}
}