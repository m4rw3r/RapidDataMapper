--TEST--
Use TrackCollectionFilter->sql() to add custom SQL, using multiple bound parameters
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()
	->has()
		->sql(':alias.name LIKE ? OR :alias.name LIKE ?',
		array('%o_l%', 'Ea%'))
	->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

$tracks = TrackCollection::create()
	->with(TrackCollection::Album)
		->has()
		->sql('LENGTH(:alias.name) < :len OR SUBSTR(:alias.name, 0, 1) = :firstchar',
			array('len' => 16, 'firstchar' => 'W'))
		->end()
	->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}
--EXPECT--
3  Earthbound
10  Owlwood
19  Following Betulas
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
20  The Cry of Silence
21  Silent Winter
22  A Slumber Did My Spirit Seal
23  The Solitude
24  Reversio Ad Secessum
25  The Amaranth
26  Akherousia
27  It Grieves My Heart