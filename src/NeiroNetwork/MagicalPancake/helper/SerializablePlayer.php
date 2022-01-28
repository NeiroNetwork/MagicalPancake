<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;

class SerializablePlayer{

	private AsynchronousDataPacketSender $packetSender;

	public float $x;
	public float $y;
	public float $z;

	public function __construct(
		public int $sessionId,
		Vector3 $position
	){
		$this->packetSender = AsynchronousDataPacketSender::getInstance();
		$this->setPosition($position);
	}

	public function setPosition(Vector3 $position) : void{
		$this->x = $position->x;
		$this->y = $position->y;
		$this->z = $position->z;
	}

	/**
	 * @param DataPacket[] $packets
	 */
	public function sendDataPackets(array $packets, bool $immediate = false) : void{
		$this->packetSender->sendTo($this->sessionId, $packets, $immediate);
	}
}