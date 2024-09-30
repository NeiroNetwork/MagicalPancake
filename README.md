# MagicalPancake
PocketMine-MPでMIDIファイルを再生するプラグイン

## 使い方
```php
use NeiroNetwork\MagicalPancake\player\MidiPlayer;

$midiPlayer = new MidiPlayer();
$midiPlayer->addRecipient($player);
$midiPlayer->load("path/to/your/midi/file.mid");
$midiPlayer->play();
```
