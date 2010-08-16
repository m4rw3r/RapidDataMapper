--TEST--
Insert an entity and monitor creation of __id and __data properties
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = new Artist();

$a->name = 'Foobar';

echo '$a: ';
var_dump($a);

ArtistCollection::persist($a);

var_dump($a);

ArtistCollection::pushChanges();

var_dump($a);

var_dump(Config::getAdapter()->query('SELECT name FROM tbl_artists WHERE id = 3')->val());
--EXPECTF--
$a: object(Artist)#%d (2) {
  ["id"]=>
  NULL
  ["name"]=>
  string(6) "Foobar"
}
object(Artist)#%d (2) {
  ["id"]=>
  NULL
  ["name"]=>
  string(6) "Foobar"
}
object(Artist)#%d (4) {
  ["id"]=>
  int(3)
  ["name"]=>
  string(6) "Foobar"
  ["__id"]=>
  array(1) {
    ["id"]=>
    int(3)
  }
  ["__data"]=>
  array(1) {
    ["name"]=>
    string(6) "Foobar"
  }
}
string(6) "Foobar"