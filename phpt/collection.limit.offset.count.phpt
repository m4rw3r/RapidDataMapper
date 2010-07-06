--TEST--
Fetch Track list, limit to 10 rows, offset by 20, and count them
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()->limit(10)->offset(20);

var_dump(count($tracks));
--EXPECT--
int(7)