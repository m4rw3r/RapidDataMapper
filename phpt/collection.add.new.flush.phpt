--TEST--
Create a new Artist, then add a new album with AlbumCollection::createFromArtist($a)->add($o) syntax, and finally flush to DB, tests if relations can be established between objects before flush
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

ArtistCollection::persist($artist);

$albums = AlbumCollection::createFromArtist($artist)->add($album);

Config::getManager()->pushChanges();

echo "===\n";

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'array search: ';
var_dump(array_search($album, $albums->contents, true));
--EXPECT--
artist->id: NULL
album->id: NULL
album->artist_id: NULL
===
artist->id: int(3)
album->id: int(4)
album->artist_id: int(3)
array search: int(0)