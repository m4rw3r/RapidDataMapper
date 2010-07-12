--TEST--
Fetch an Artist (id: 1) and its albums, then add() an existing Album from the same collection
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

echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'array search: ';
var_dump(array_search($album, $artist->albums->contents, true));

$artist->albums->add($album);

echo "===\n";

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'array search: ';
var_dump(array_search($album, $artist->albums->contents, true));
// Should not be changed at all, as it is only assigned from its own collection
--EXPECT--
album->id: int(2)
album->artist_id: int(1)
array search: int(2)
===
artist->id: int(1)
album->id: int(2)
album->artist_id: int(1)
array search: int(2)