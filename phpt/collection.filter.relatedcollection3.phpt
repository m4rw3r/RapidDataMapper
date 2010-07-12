--TEST--
Fetch Album list and filter with two tracks (from two different albums) separated by AND
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$t1 = TrackCollection::fetchByPrimaryKey(1);
$t2 = TrackCollection::fetchByPrimaryKey(10);

$albums = AlbumCollection::create()
	->has()->relatedTracks($t1)->end()
	->has()->relatedTracks($t2)->end();

echo 'count: ';
var_dump(count($albums));
--EXPECT--
count: int(0)