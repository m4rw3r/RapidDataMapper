--TEST--
Fetch an object and then modify it and push changes
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = ArtistCollection::fetchByPrimaryKey(1);

$a->name = 'Foobar';

ArtistCollection::pushChanges();

var_dump(Config::getAdapter()->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
--EXPECT--
string(6) "Foobar"