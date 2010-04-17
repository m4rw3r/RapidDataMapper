--TEST--
Fetch Track list with simple AND conditions
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()->has()->name('Seasons Apart')->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

$tracks = TrackCollection::create()->has()->artist_id(2)->album_id(3)->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

$tracks = TrackCollection::create()->has()->artist_id(2)->album_id(3)->name('Owlwood')->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}
--EXPECT--
1  Seasons Apart
===
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
