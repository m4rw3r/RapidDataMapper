--TEST--
Fetch the first artist by primary key (1), then try to fetch a non-existing entry
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = ArtistCollection::fetchByPrimaryKey(1);

echo 'a->id: ';
var_dump($a->id);
echo 'a->name: ';
var_dump($a->name);

$a = ArtistCollection::fetchByPrimaryKey(12345);
echo 'non_existing: ';
var_dump($a);
--EXPECT--
a->id: int(1)
a->name: string(9) "Draconian"
non_existing: bool(false)