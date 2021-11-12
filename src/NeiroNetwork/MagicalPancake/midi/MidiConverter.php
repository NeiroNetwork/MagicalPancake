<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi;

use Tmont\Midi\Delta;
use Tmont\Midi\Event\ControllerEvent;
use Tmont\Midi\Event\EventType;
use Tmont\Midi\Event\NoteOnEvent;
use Tmont\Midi\Event\SetTempoEvent;
use Tmont\Midi\Event\TrackNameEvent;
use Tmont\Midi\FileHeader;
use Tmont\Midi\Parsing\FileParser;
use Tmont\Midi\TrackHeader;
use Tmont\Midi\Util\Controller;

class MidiConverter{

	public static function midiToStream(string $file) : array{
		$parser = new FileParser();
		$parser->load($file);

		$playData = [];
		$ticksPerBeat = null;
		$currentTick = 0;
		$trackName = "note.harp";
		$trackVolume = 100;

		while($chunk = $parser->parse()){
			if($chunk instanceof FileHeader){
				$ticksPerBeat = $chunk->getData()[2];
			}elseif($chunk instanceof TrackHeader){
				$currentTick = 0;
				$trackName = "note.harp";
				$trackVolume = 100;
			}elseif($chunk instanceof TrackNameEvent){
				$trackName = $chunk->getData()[2];
				if(empty($trackName)) $trackName = "note.harp";
			}elseif($chunk instanceof ControllerEvent){
				if($chunk->getType() === EventType::CONTROLLER){
					$data = $chunk->getData();
					if($data[1] === Controller::MAIN_VOLUME){
						$trackVolume = $data[2];
					}
				}
			}elseif($chunk instanceof SetTempoEvent){
				$data = $chunk->getData()[2];
				$mpqn = ($data[0] << 16) | ($data[1] << 8) | $data[2];
				$playData[$currentTick][] = ["mpqn", $mpqn / 1000000];
			}elseif($chunk instanceof NoteOnEvent){
				[, $note, $velocity] = $chunk->getData();
				$volume = ($velocity / 100) * ($trackVolume / 100);
				if($volume > 0){
					$playData[$currentTick][] = [$trackName, $note, $volume];
				}
			}elseif($chunk instanceof Delta){
				$currentTick += $chunk->getData()[0];
			}
		}

		unset($parser);
		ksort($playData);
		assert(!is_null($ticksPerBeat));

		$result = [];
		$mpqn = 0;
		$time = 0;
		$currentTick = 0;
		foreach($playData as $tick => $notes){
			foreach($notes as $note){
				if($note[0] === "mpqn"){
					$mpqn = $note[1];
					continue;
				}
				$result[$tick][1][] = $note;
			}
			$diff = $tick - $currentTick;
			$currentTick = $tick;
			$time += $mpqn * $diff / $ticksPerBeat;
			if(!empty($result[$tick][1])){
				$result[$tick][0] = $time;
			}
		}

		return $result;
	}

	public static function noteToPitch(int $note, string $sound = "") : float{
		$pitches = [1.41, 1.5, 1.59, 1.68333, 1.78, 1.8875, 1 * 2, 1.06 * 2, 1.1225 * 2, 1.19 * 2, 1.26 * 2, 1.33333 * 2];
		$octaveFixes = ["note.bass" => 2.0, "note.bassattack" => 2.0];

		$octave = 2 ^ (floor($note / 12) - 6);
		return $pitches[$note % 12] * $octave * ($octaveFixes[$sound] ?? 1.0);
	}
}