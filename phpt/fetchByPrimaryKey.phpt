--TEST--
Fetch the first artist by primary key (1), then try to fetch a non-existing entry
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = ArtistCollection::fetchByPrimaryKey(1);

echo "$a->id\n";
echo "$a->name\n";

$a = ArtistCollection::fetchByPrimaryKey(12345);
var_dump($a);
--EXPECT--
1
Draconian
bool(false)