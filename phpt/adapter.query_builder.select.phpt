--TEST--
Use the Rdm_Query_Select class to build and execute a SELECT query
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = Rdm_Adapter::getInstance();

var_dump($a->select()->from('artists', 'name')->limit(1)->get()->val());
--EXPECT--
string(9) "Draconian"