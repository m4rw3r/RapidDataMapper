--TEST--
Test serialization of collection objects
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = new ArtistCollection();
$a->with(ArtistCollection::Tracks);

$s = serialize($a);

$b = unserialize($s);

echo 'classname $a: ';
var_dump(get_class($a));
echo 'classname $b: ';
var_dump(get_class($b));
echo '$a === $b: ';
var_dump($a === $b);
echo '$a == $b: ';
var_dump($a == $b);
echo '$a->contents === $b->contents: ';
var_dump($a->contents === $b->contents);
echo 'count($a->contents) === count($b->contents): ';
var_dump(count($a->contents) == count($b->contents));
echo '$a is locked: ';
var_dump($a->is_locked);
echo '$b is locked: ';
var_dump($b->is_locked);

echo "===\na\n===\n";

foreach($a as $artist)
{
	echo "$artist->id  $artist->name ".get_class($artist->tracks)."\n";
	foreach($artist->tracks as $t)
	{
		echo "$t->id ";
	}
	echo "\n";
}

echo "===\nb\n===\n";

foreach($b as $artist)
{
	echo "$artist->id  $artist->name ".get_class($artist->tracks)."\n";
	foreach($artist->tracks as $t)
	{
		echo "$t->id ";
	}
	echo "\n";
}

--EXPECT--
classname $a: string(16) "ArtistCollection"
classname $b: string(16) "ArtistCollection"
$a === $b: bool(false)
$a == $b: bool(false)
$a->contents === $b->contents: bool(false)
count($a->contents) === count($b->contents): bool(true)
$a is locked: bool(true)
$b is locked: bool(true)
===
a
===
1  Draconian TrackCollection
1 2 3 4 5 6 7 8 9 20 21 22 23 24 25 26 27 
2  Cult of Luna TrackCollection
10 11 12 13 14 15 16 17 18 19 
===
b
===
1  Draconian TrackCollection
1 2 3 4 5 6 7 8 9 20 21 22 23 24 25 26 27 
2  Cult of Luna TrackCollection
10 11 12 13 14 15 16 17 18 19