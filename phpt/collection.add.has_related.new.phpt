--TEST--
Fetch an Artist (id: 1) and its albums, then add() a newly created album to the list
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$artist = ArtistCollection::fetchByPrimaryKey(1);

$albums = AlbumCollection::create()->has()->relatedArtist($artist)->end();

$album = new Album();
$album->name = 'Arcane Rain Fell';

$albums->add($album);

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'array search: ';
var_dump(array_search($album, $albums->contents, true));
echo 'count contents: ';
var_dump(count($albums->contents));
// If there are more than one entity in $albums, then it means that it fetched it,
// which is wong as it is really unnecessary to fetch the whole album list for
// just adding a single album
--EXPECT--
artist->id: int(1)
album->id: NULL
album->artist_id: int(1)
array search: int(0)
count contents: int(1)