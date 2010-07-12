--TEST--
Fetch Track list, limit to 10 rows, count, offset by 20, and count them again
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()->limit(10);

echo 'before: ';
var_dump(count($tracks));

$tracks->offset(20);

echo 'with offset: ';
var_dump(count($tracks));
--EXPECT--
before: int(10)
with offset: int(7)