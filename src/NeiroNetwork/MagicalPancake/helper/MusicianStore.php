<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\helper;

use NeiroNetwork\MagicalPancake\musician\AsyncMidiMusician;

class MusicianStore{

	private static self $instance;

	public static function getInstance() : self{
		return self::$instance ?? self::$instance = new self;
	}

	/** @var AsyncMidiMusician[] */
	private array $musicians = [];

	public function getMusicians() : array{
		return $this->musicians;
	}

	public function addMusician(AsyncMidiMusician $musician) : void{
		$this->musicians[spl_object_id($musician)] = $musician;
	}

	public function removeMusician(AsyncMidiMusician $musician) : void{
		unset($this->musicians[spl_object_id($musician)]);
	}
}