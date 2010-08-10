--TEST--
Fetch an Artist (id: 1) and its albums, then remove album (id: 1) from collection with unset($albums[$id])
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

$album = $artist->albums[1];

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'array search: ';
var_dump(array_search($album, $artist->albums->contents, true));
echo "===\n";

// Remove
unset($artist->albums[$album->id]);

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->id: ';
var_dump($album->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'array search: ';
var_dump(array_search($album, $artist->albums->contents, true));

echo "===\n";
ArtistCollection::pushChanges();

echo 'db check: ';
var_dump(Config::getAdapter()->query('SELECT artist_id FROM tbl_albums WHERE id = 1')->val());
--EXPECT--
artist->id: int(1)
album->id: int(1)
album->artist_id: int(1)
array search: int(1)
===
artist->id: int(1)
album->id: int(1)
album->artist_id: NULL
array search: bool(false)
===
db check: NULL