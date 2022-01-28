# MagicalPancake
PocketMine-MPでMIDIファイルを再生するプラグイン

## :warning: 注意
- `pocketmine.yml` の `enable-encryption` を `false` にしないと動作しません

## 使い方
`NeiroNetwork\MagicalPancake\MidiPlayer` にある関数を呼び出します。  
[EvalBook](https://github.com/NeiroNetwork/EvalBook) などを使うと簡単です。
```php
use NeiroNetwork\MagicalPancake\MidiPlayer;

// MIDIファイルを再生する
MidiPlayer::play("my_midi_file.mid");
// 演奏を止める
MidiPlayer::stop();
```
