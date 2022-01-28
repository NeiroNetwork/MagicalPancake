<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi\event;

class NotesOn extends TimeBaseMidiEvent{

	/** @param NoteOn[] $notes */
	public function __construct(
		private array $notes
	){}

	public function getNotes() : array{
		return $this->notes;
	}
}