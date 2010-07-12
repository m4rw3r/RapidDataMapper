--TEST--
Fetch an artist (id: 1) and its albums, then add a new album with $l[] = $o; syntax
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

$artist->albums[] = $album;

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'array search: ';
var_dump(array_search($album, $artist->albums->contents, true));
--EXPECT--
artist->id: int(1)
album->id: NULL
album->artist_id: int(1)
array search: int(3)