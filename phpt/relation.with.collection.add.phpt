--TEST--
Fetch an Artist (id: 1) and its albums, then add another album with $l->add($o) syntax
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

$artist->albums->add($album);

var_dump($artist->id);
var_dump($album->id);
var_dump($album->artist_id);
var_dump(array_search($album, $artist->albums->getContentReference()));
--EXPECT--
int(1)
NULL
int(1)
int(3)