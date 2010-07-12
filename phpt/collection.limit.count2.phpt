--TEST--
Fetch Track list, count, limit to 10 rows, and count them again
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create();

echo 'before: ';
var_dump(count($tracks));

$tracks->limit(10);

echo 'with limit: ';
var_dump(count($tracks));
--EXPECT--
before: int(27)
with limit: int(10)