--TEST--
offsetGet() of the ArrayAccess interface
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$ac = ArtistCollection::create();

echo 'ac[1]->id: ';
var_dump($ac[1]->id);
echo 'ac[1]->name: ';
var_dump($ac[1]->name);
echo 'ac[2]->id: ';
var_dump($ac[2]->id);
echo 'ac[2]->name: ';
var_dump($ac[2]->name);
echo 'ac[3]->id: ';
var_dump($ac[3]->id);
echo 'ac[3]->name: ';
var_dump($ac[3]->name);

--EXPECTF--
ac[1]->id: int(1)
ac[1]->name: string(9) "Draconian"
ac[2]->id: int(2)
ac[2]->name: string(12) "Cult of Luna"
ac[3]->id: 
Notice: Undefined offset: 3 in %s on line %d

Notice: Trying to get property of non-object in %s on line %d
NULL
ac[3]->name: 
Notice: Undefined offset: 3 in %s on line %d

Notice: Trying to get property of non-object in %s on line %d
NULL