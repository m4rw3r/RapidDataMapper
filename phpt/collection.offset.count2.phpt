--TEST--
Fetch Track list, count, offset 10 rows, and count the rest
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create();

var_dump(count($tracks));

$tracks->offset(10);

try
{
	var_dump(count($tracks));
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}
--EXPECT--
int(27)
string(58) "OFFSET without LIMIT is not supported by the SQL standard."