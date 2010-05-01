--TEST--
Check if a collection is empty if it matches all tracks and then if it matches none
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create();

var_dump($tracks->isEmpty());

$tracks = TrackCollection::create()
	->has()->id(3458345)->end();

var_dump($tracks->isEmpty());
--EXPECT--
bool(false)
bool(true)
