<?php

declare(strict_types=1);

namespace NeiroNetwork\MagicalPancake\midi;

use NeiroNetwork\MagicalPancake\midi\event\NoteOn;
use NeiroNetwork\MagicalPancake\midi\event\NotesOn;
use NeiroNetwork\MagicalPancake\midi\event\Rest;
use NeiroNetwork\MagicalPancake\midi\event\SetTempo;
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

class MidiFileConverter{

	public static function convert(string $filePath) : TimeBasedEventStream{
		$parser = new FileParser();
		$parser->load($filePath);

		self::parseFile($parser, $ticksPerBeat, $events);
		return self::toStream($ticksPerBeat, $events);
	}

	private static function toStream(int $ticksPerBeat, TickBasedEvents $tickBasedEvents) : TimeBasedEventStream{
		$stream = new TimeBasedEventStream();

		$beforeTick = 0;
		$currentSpqn = 0;
		foreach($tickBasedEvents->getEvents() as $tick => $events){
			$time = $currentSpqn * ($tick - $beforeTick) / $ticksPerBeat;
			if($time > 0){
				$stream->addEvent(new Rest($time));
			}
			$beforeTick = $tick;

			$notes = [];
			foreach($events as $event){
				if($event instanceof SetTempo){
					$currentSpqn = $event->getSecondsPerQuarterNote();
				}elseif($event instanceof NoteOn){
					$notes[] = $event;
				}
			}
			if(!empty($notes)){
				$stream->addEvent(new NotesOn($notes));
			}
		}

		return $stream;
	}

	private static function parseFile(FileParser $parser, ?int &$ticksPerBeat, ?TickBasedEvents &$events) : void{
		$ticksPerBeat = 0;
		$events = new TickBasedEvents();

		$track = new Track();
		while($chunk = $parser->parse()){
			if($chunk instanceof FileHeader){
				$ticksPerBeat = $chunk->getData()[2];
			}elseif($chunk instanceof TrackHeader){
				$track = new Track();
			}elseif($chunk instanceof TrackNameEvent){
				$track->setName($chunk->getData()[2] ?? "note.harp");
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
				$events->addEvent($track->getTick(), new SetTempo($mpqn));
			}elseif($chunk instanceof NoteOnEvent){
				[, $note, $velocity] = $chunk->getData();
				$events->addEvent($track->getTick(), new NoteOn($note, $track->getName(), $velocity, $track->getVolume()));
			}elseif($chunk instanceof Delta){
				$track->addTick($chunk->getData()[0]);
			}
		}

		unset($parser);

		assert($ticksPerBeat > 0);
		$events->normalize();
	}
}