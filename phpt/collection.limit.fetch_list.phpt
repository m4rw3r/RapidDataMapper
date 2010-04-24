--TEST--
Fetch Track list, limit to 10 rows, and iterate all tracks
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()->limit(10);

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}
--EXPECT--
1  Seasons Apart
2  When I Wake
3  Earthbound
4  Not Breathing
5  The Failure Epiphany
6  Morphine Cloud
7  Bloodflower
8  The Empty Stare
9  September Ashes
10  Owlwood