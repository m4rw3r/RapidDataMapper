--TEST--
Fetch an Artist (id: 1) and its albums, then add() an existing Album
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

$album = AlbumCollection::fetchByPrimaryKey(2);

var_dump(array_search($album, $artist->albums->getContentReference()));

$artist->albums->add($album);

var_dump($artist->id);
var_dump($album->id);
var_dump($album->artist_id);
var_dump(array_search($album, $artist->albums->getContentReference()));
--EXPECT--
int(2)
int(1)
int(2)
int(1)
int(2)