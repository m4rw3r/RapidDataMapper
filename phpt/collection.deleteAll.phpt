--TEST--
Delete all artists from the database using $a->deleteAll();
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = ArtistCollection::create();

$a->deleteAll();

Config::getManager()->pushChanges();

var_dump(Config::getAdapter()->query('SELECT COUNT(1) FROM tbl_artists')->val());
--EXPECT--
string(1) "0"