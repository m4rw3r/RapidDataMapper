--TEST--
Save an object in the database
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = new Artist();
$a->name = 'foobar';

echo 'a->id: ';
var_dump($a->id);
echo 'a->name: ';
var_dump($a->name);
echo 'db check: ';
var_dump(Config::getAdapter()->query('SELECT id FROM tbl_artists WHERE id = 3')->val());

ArtistCollection::persist($a);

Config::getManager()->pushChanges();

echo "===\n";

echo 'a->id: ';
var_dump($a->id);
echo 'a->name: ';
var_dump($a->name);
echo 'db check: ';
var_dump(Config::getAdapter()->query('SELECT id FROM tbl_artists WHERE id = 3')->val());
--EXPECT--
a->id: NULL
a->name: string(6) "foobar"
db check: bool(false)
===
a->id: int(3)
a->name: string(6) "foobar"
db check: string(1) "3"