--TEST--
Fetch Track list with filter by a list of artists stored in a collection
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$c = ArtistCollection::create()->has()->name('Cult of Luna')->end();
$tracks = TrackCollection::create()->has()->relatedArtistCollection($c)->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

$tracks = TrackCollection::create()->has()->relatedArtistCollection($c)->name('Owlwood')->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

// Get the ids of the artists which has an album with the name Turning Season Within
$c = ArtistCollection::create()
	->with(ArtistCollection::Albums)
		->has()
		->name('Turning Season Within')
		->end()
	->end();

// Get the tracks of this artist
$tracks = TrackCollection::create()->has()->relatedArtistCollection($c)->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}
--EXPECT--
10  Owlwood
11  Eternal Kingdom
12  Ghost Trail
13  The Lure (Interlude)
14  Mire Deep
15  The Great Migration
16  Österbotten
17  Curse
18  Ugin
19  Following Betulas
===
10  Owlwood
===
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