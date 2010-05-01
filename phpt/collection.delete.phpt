--TEST--
Fetch an object and then delete it from db
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = ArtistCollection::fetchByPrimaryKey(1);

ArtistCollection::delete($a);

ArtistCollection::pushChanges();

var_dump(Rdm_Adapter::getInstance()->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
--EXPECT--
bool(false)