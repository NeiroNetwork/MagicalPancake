<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi;

use NeiroNetwork\MagicalPancake\midi\event\NoteOn;

class NoteConverter{

	public const PITCHES = [1.41, 1.5, 1.59, 1.68333, 1.78, 1.8875, 1 * 2, 1.06 * 2, 1.1225 * 2, 1.19 * 2, 1.26 * 2, 1.33333 * 2];
	public const OCTAVE_FIXES = [
		"note.bass" => 4.0,
		"note.bassattack" => 4.0,
		"note.flute" => 0.5,
		"note.guitar" => 2.0,
		"mob.villager.death" => 1.404,
	];

	public static function toPitch(NoteOn $note) : float{
		$pitch = self::PITCHES[$note->getNote() % 12];
		$octave = 2 ** (floor($note->getNote() / 12) - 6);
		$fixed = self::OCTAVE_FIXES[$note->getSound()] ?? 1.0;
		return $pitch * $octave * $fixed;
	}

	public static function toVolume(NoteOn $note) : float{
		return ($note->getVolume() / 127) * (($note->getVelocity() / 127) ** 2);
	}
}