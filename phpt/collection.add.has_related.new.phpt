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

var_dump($artist->id);
var_dump($album->id);
var_dump($album->artist_id);
var_dump(array_search($album, $albums->contents, true));
var_dump(count($albums->contents));
// If there are more than one entity in $albums, then it means that it fetched it,
// which is wong as it is really unnecessary to fetch the whole album list for
// just adding a single album
--EXPECT--
int(1)
NULL
int(1)
int(0)
int(1)