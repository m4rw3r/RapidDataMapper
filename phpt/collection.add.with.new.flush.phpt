--TEST--
Fetch an Artist (id: 1) and its albums, then add a new album with $l->add($o) syntax, and finally flush to DB
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$artists = ArtistCollection::create()
	->with(ArtistCollection::Albums)
	->end()
	->has()->id(1)->end();

$artist = $artists[1];

$album = new Album();
$album->name = 'Arcane Rain Fell';

echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'array search: ';
var_dump(array_search($album, $artist->albums->contents, true));

$artist->albums->add($album);

Config::getManager()->pushChanges();

echo "===\n";

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'array search: ';
var_dump(array_search($album, $artist->albums->contents, true));
--EXPECT--
album->id: NULL
album->artist_id: NULL
array search: bool(false)
===
artist->id: int(1)
album->id: int(4)
album->artist_id: int(1)
array search: int(3)