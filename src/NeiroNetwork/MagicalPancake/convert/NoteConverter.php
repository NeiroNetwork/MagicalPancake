<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert;

use NeiroNetwork\MagicalPancake\convert\midi\event\NoteOn;

class NoteConverter{

	private const OCTAVE_FIXES = [
		"note.bass" => 2,
		"note.bassattack" => 2,
		"note.flute" => -1,
		"note.guitar" => 1,
	];

	private const PITCH_FIXES = [
		"mob.villager.death" => 1.404,
	];

	public static function toVolume(NoteOn $note) : float{
		return ($note->getVolume() / 127) * (($note->getVelocity() / 127) ** 2);
	}

	public static function toPitch(NoteOn $note) : float{
		$octave = self::OCTAVE_FIXES[$note->getName()] ?? 0;

		$array = explode(".", $note->getName());
		if(is_numeric($n = end($array))){
			$octave -= $n - 4;
		}

		// F#4 = 66
		$pitch = 2 ** (($note->getNote() - 66 + $octave * 12) / 12);
		return $pitch * (self::PITCH_FIXES[$note->getName()] ?? 1);

		// TODO: ピッチベンドも考慮する
	}
}