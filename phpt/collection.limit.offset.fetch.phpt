--TEST--
Fetch Track list, limit to 10 rows offset by 20, and print them
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()->limit(10)->offset(20);

foreach($tracks as $t)
{
	echo "$t->id $t->name\n";
}
--EXPECT--
21 Silent Winter
22 A Slumber Did My Spirit Seal
23 The Solitude
24 Reversio Ad Secessum
25 The Amaranth
26 Akherousia
27 It Grieves My Heart