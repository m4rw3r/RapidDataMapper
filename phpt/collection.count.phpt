--TEST--
Count the number of tracks in Db
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create();

var_dump(count($tracks));
--EXPECT--
int(27)