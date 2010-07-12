--TEST--
Fetch an Artist (id: 1) and its albums, then remove album (id: 1) from collection with remove($album)
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

var_dump($artist->id);
var_dump($album->id);
var_dump($album->artist_id);
echo "===\n";

$artist->albums->remove($album);

var_dump($album->artist_id);
var_dump(array_search($album, $artist->albums->contents, true));

echo "===\n";
ArtistCollection::pushChanges();
var_dump(Config::getAdapter()->query('SELECT artist_id FROM tbl_albums WHERE id = 1')->val());
--EXPECT--
int(1)
int(1)
int(1)
===
NULL
bool(false)
===
string(1) "0"