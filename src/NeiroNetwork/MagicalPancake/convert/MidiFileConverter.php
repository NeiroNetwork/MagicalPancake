<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\convert;

use NeiroNetwork\MagicalPancake\convert\midi\event\MidiEvent;
use NeiroNetwork\MagicalPancake\convert\midi\event\NoteOn;
use NeiroNetwork\MagicalPancake\convert\midi\event\SetTempo;
use NeiroNetwork\MagicalPancake\convert\midi\ParsedMidiData;
use NeiroNetwork\MagicalPancake\convert\midi\part\Track;
use NeiroNetwork\MagicalPancake\convert\stream\event\PlayNotesEvent;
use NeiroNetwork\MagicalPancake\convert\stream\event\RestEvent;
use NeiroNetwork\MagicalPancake\convert\stream\MusicPlayStream;
use NeiroNetwork\MagicalPancake\convert\stream\part\MinecraftNote;
use Tmont\Midi\Delta;
use Tmont\Midi\Event\ControllerEvent;
use Tmont\Midi\Event\EventType;
use Tmont\Midi\Event\NoteOnEvent;
use Tmont\Midi\Event\PitchBendEvent;
use Tmont\Midi\Event\SetTempoEvent;
use Tmont\Midi\Event\TrackNameEvent;
use Tmont\Midi\FileHeader;
use Tmont\Midi\Parsing\FileParser;
use Tmont\Midi\TrackHeader;
use Tmont\Midi\Util\Controller;

class MidiFileConverter{

	public static function convert(string $filePath) : MusicPlayStream{
		$parser = new FileParser();
		$parser->load($filePath);

		return self::toStream(self::parseFile($parser));
	}

	private static function parseFile(FileParser $parser) : ParsedMidiData{
		$result = new ParsedMidiData();

		$track = new Track();
		while($chunk = $parser->parse()){
			if($chunk instanceof FileHeader){
				$result->setTicksPerBeat($chunk->getData()[2]);
			}elseif($chunk instanceof TrackHeader){
				$track = new Track();
			}elseif($chunk instanceof TrackNameEvent){
				$name = trim($chunk->getData()[2] ?? "");
				$track->setName(empty($name) ? "note.harp" : $name);
			}elseif($chunk instanceof ControllerEvent){
				if($chunk->getType() === EventType::CONTROLLER){
					$data = $chunk->getData();
					if($data[1] === Controller::MAIN_VOLUME){
						$track->setVolume($data[2]);
					}
				}
			}elseif($chunk instanceof SetTempoEvent){
				$data = $chunk->getData()[2];
				$mpqn = ($data[0] << 16) | ($data[1] << 8) | $data[2];
				$result->addEvent($track->getCurrentTick(), new SetTempo($mpqn));
			}elseif($chunk instanceof NoteOnEvent){
				[, $note, $velocity] = $chunk->getData();
				$result->addEvent($track->getCurrentTick(), new NoteOn($track, $note, $velocity));
			}elseif($chunk instanceof Delta){
				$track->addCurrentTick($chunk->getData()[0]);
			}elseif($chunk instanceof PitchBendEvent){
				// TODO: Dominoでは期待通りのデータが得られなかった
			}
		}

		unset($parser);

		$result->finalize();
		return $result;
	}

	private static function toStream(ParsedMidiData $data) : MusicPlayStream{
		$stream = new MusicPlayStream();

		$beforeTick = 0;
		$currentSpqn = 0;
		foreach($data->getEvents() as $tick => $events){
			$time = $currentSpqn * ($tick - $beforeTick) / $data->getTicksPerBeat();
			if($time > 0) $stream->push(new RestEvent($time));
			$beforeTick = $tick;

			$notes = new PlayNotesEvent();
			foreach($events as $event){
				if($event instanceof SetTempo){
					$currentSpqn = $event->getSecondsPerQuarterNote();
				}elseif($event instanceof NoteOn){
					$notes->add(new MinecraftNote($event->getName(), NoteConverter::toVolume($event), NoteConverter::toPitch($event)));
				}
			}
			if(!$notes->isEmpty()){
				$stream->push($notes);
			}
		}

		return $stream;
	}
}