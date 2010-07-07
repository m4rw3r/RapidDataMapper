--TEST--
Save an object in the database
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = new Artist();
$a->name = 'foobar';

ArtistCollection::persist($a);

Config::getManager()->pushChanges();

var_dump($a->id);
var_dump($a->name);
var_dump(Config::getAdapter()->query('SELECT id FROM tbl_artists WHERE id = 3')->val());
--EXPECT--
int(3)
string(6) "foobar"
string(1) "3"