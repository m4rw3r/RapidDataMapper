--TEST--
Remove an album from a list of albums of an artist without fetching all the albums of the artist
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$artist = ArtistCollection::fetchByPrimaryKey(1);

$album = AlbumCollection::fetchByPrimaryKey(1);

echo 'artist->id: ';
var_dump($artist->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);

// Save the collection so we can count the number of entities in it 0 = none were fetched
$a = AlbumCollection::createFromArtist($artist)->remove($album);

echo "===\n";
echo 'artist->id: ';
var_dump($artist->id);
echo 'album->artist_id: ';
var_dump($album->artist_id);
echo 'collection count: ';
var_dump(count($a->contents));
--EXPECT--
artist->id: int(1)
album->artist_id: int(1)
===
artist->id: int(1)
album->artist_id: NULL
collection count: int(0)