--TEST--
Fetch an object and then delete it from db, do NOT push changes
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = ArtistCollection::fetchByPrimaryKey(1);

ArtistCollection::delete($a);

var_dump(Config::getAdapter()->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
--EXPECT--
string(9) "Draconian"