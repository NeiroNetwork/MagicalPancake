<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert\stream\event;

use NeiroNetwork\MagicalPancake\convert\stream\part\MinecraftNote;
use pmmp\thread\ThreadSafeArray;

final class PlayNotesEvent extends NoteEvent{

	/**
	 * @param ThreadSafeArray<int, MinecraftNote> $notes
	 */
	public function __construct(
		private ThreadSafeArray $notes = new ThreadSafeArray(),
	){}

	/** @return MinecraftNote[] */
	public function getNotes() : array{
		return (array) $this->notes;
	}

	public function add(MinecraftNote $note) : void{
		$this->notes[] = $note;
	}

	public function isEmpty() : bool{
		return $this->notes->count() === 0;
	}
}
