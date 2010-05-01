--TEST--
Fetch a list of Track objects based on an already fetched artist object
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$artist = ArtistCollection::fetchByPrimaryKey(1);

$tracks = TrackCollection::create()->has()->relatedArtist($artist)->end();

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
20  The Cry of Silence
21  Silent Winter
22  A Slumber Did My Spirit Seal
23  The Solitude
24  Reversio Ad Secessum
25  The Amaranth
26  Akherousia
27  It Grieves My Heart