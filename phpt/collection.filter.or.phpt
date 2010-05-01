--TEST--
Fetch Track list with simple OR conditions
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()->has()->artist_id(2)->end()->orHas()->album_id(2)->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

$tracks = TrackCollection::create()->has()->artist_id(2)->name('Owlwood')->end()->orHas()->album_id(2)->name('Akherousia')->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

$tracks = TrackCollection::create()->orHas()->artist_id(2)->name('Owlwood')->end()->orHas()->album_id(2)->name('Akherousia')->end();

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
20  The Cry of Silence
21  Silent Winter
22  A Slumber Did My Spirit Seal
23  The Solitude
24  Reversio Ad Secessum
25  The Amaranth
26  Akherousia
27  It Grieves My Heart
===
10  Owlwood
26  Akherousia
===
10  Owlwood
26  Akherousia
