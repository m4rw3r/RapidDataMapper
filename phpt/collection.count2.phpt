--TEST--
Count the number of tracks in Db, alternate syntax
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create();

echo 'count: ';
var_dump($tracks->count());
--EXPECT--
count: int(27)