<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi;

class MidiConverter{

	public static function noteToPitch(int $note, string $sound = "") : float{
		$pitches = [1.41, 1.5, 1.59, 1.68333, 1.78, 1.8875, 1 * 2, 1.06 * 2, 1.1225 * 2, 1.19 * 2, 1.26 * 2, 1.33333 * 2];
		$octaveFixes = [
			"note.bass" => 4.0,
			"note.bassattack" => 4.0,
			"note.flute" => 0.5,
			"note.guitar" => 2.0,
			"mob.villager.death" => 1.404,
		];

		$octave = 2 ** (floor($note / 12) - 6);
		return $pitches[$note % 12] * $octave * ($octaveFixes[$sound] ?? 1.0);
	}
}