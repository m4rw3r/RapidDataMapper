--TEST--
Fetch an Artist (id: 1) and its albums, then add another album with $l->add($o) syntax, and finally flush to DB
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

Rdm_Collection::pushChanges();

var_dump($artist->id);
var_dump($album->id);
var_dump($album->artist_id);
var_dump(array_search($album, $artist->albums->getContentReference(), true));
--EXPECT--
int(1)
int(4)
int(1)
int(3)