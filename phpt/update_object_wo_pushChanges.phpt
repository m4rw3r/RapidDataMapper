--TEST--
Fetch an object and then modify it and DON'T push changes
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = ArtistCollection::fetchByPrimaryKey(1);

$a->name = 'Foobar';

var_dump(Rdm_Adapter::getInstance()->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
--EXPECT--
string(9) "Draconian"