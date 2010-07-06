--TEST--
Fetch Track list, count, limit to 10 rows, and count them again
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create();

var_dump(count($tracks));

$tracks->limit(10);

var_dump(count($tracks));
--EXPECT--
int(27)
int(10)