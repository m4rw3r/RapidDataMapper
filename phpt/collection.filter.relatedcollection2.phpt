--TEST--
Fetch Album list and filter with two tracks (from two different albums) separated by OR
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$t1 = TrackCollection::fetchByPrimaryKey(1);
$t2 = TrackCollection::fetchByPrimaryKey(10);

$albums = AlbumCollection::create()
	->has()->relatedTracks($t1)->end()
	->orHas()->relatedTracks($t2)->end();

foreach($albums as $al)
{
	echo "$al->id $al->name\n";
}
--EXPECT--
1 Turning Season Within
3 Eternal Kingdom