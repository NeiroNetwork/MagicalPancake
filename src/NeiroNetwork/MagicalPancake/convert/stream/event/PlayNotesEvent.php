<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\stream\event;

use NeiroNetwork\MagicalPancake\convert\stream\part\MinecraftNote;

class PlayNotesEvent extends NoteEvent{

	/**
	 * @param MinecraftNote[] $notes
	 */
	public function __construct(
		private array $notes = []
	){}

	public function getNotes() : array{
		return $this->notes;
	}

	public function add(MinecraftNote $note) : void{
		$this->notes[] = $note;
	}

	public function isEmpty() : bool{
		return empty($this->notes);
	}
}