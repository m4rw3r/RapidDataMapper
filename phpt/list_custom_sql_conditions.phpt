--TEST--
Use TrackCollectionFilter->sql() to add custom SQL
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()->has()->sql(':alias.name LIKE \'%o_l%\'')->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

$tracks = TrackCollection::create()
	->with(TrackCollection::Album)
		->has()->sql('LENGTH(:alias.name) > 18')->end()
	->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

$tracks = TrackCollection::create()->has()->sql(':alias.name LIKE ?', '%o_l%')->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}

echo "===\n";

$tracks = TrackCollection::create()
	->with(TrackCollection::Album)
		->has()->sql('LENGTH(:alias.name) > ?', 18)->end()
	->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}
--EXPECT--
10  Owlwood
19  Following Betulas
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
===
10  Owlwood
19  Following Betulas
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