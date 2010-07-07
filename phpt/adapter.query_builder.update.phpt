--TEST--
Use the Rdm_Query_Update class to create and execute an UPDATE query
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = Config::getAdapter();

var_dump($a->update('artists')->set('name', 'foobar')->where('id', 1)->execute());
var_dump($a->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
--EXPECT--
int(1)
string(6) "foobar"