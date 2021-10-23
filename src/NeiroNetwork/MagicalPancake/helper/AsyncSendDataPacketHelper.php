<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\Server;
use raklib\server\ipc\UserToRakLibThreadMessageSender;

class AsyncSendDataPacketHelper{

	private static self $instance;

	public static function getInstance() : self{
		return self::$instance ?? self::$instance = new self();
	}

	private UserToRakLibThreadMessageSender $interface;

	public function __construct(){
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