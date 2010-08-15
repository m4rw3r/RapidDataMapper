--TEST--
Create a new Artist, then add a new album with $o->artist = $a syntax, and finally flush to DB, tests if relations can be established between objects before flush
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$artist = new Artist();
$artist->name = 'Epica';

$album = new Album();
$album->name = 'Quietus';

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);

$album->artist = $artist;

AlbumCollection::persist($album);
ArtistCollection::persist($artist);

Config::getManager()->pushChanges();

echo "===\n";

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
--EXPECT--
artist->id: NULL
album->id: NULL
album->artist_id: NULL
===
artist->id: int(3)
album->id: int(4)
album->artist_id: int(3)