--TEST--
Use the Rdm_Query_Insert class to create and execute an INSERT query
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = Config::getAdapter();

var_dump($a->insert('artists')->set('name', 'foobar')->execute());
var_dump($a->query('SELECT name FROM tbl_artists WHERE id = 3')->val());
--EXPECT--
int(1)
string(6) "foobar"