--TEST--
Test serialization of collection objects and their ties to the Unit Of Work, with with()
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = new ArtistCollection();
$a->with(ArtistCollection::Albums);

$s = serialize($a);

$b = unserialize($s);

echo "In Unit of Work: \n";
echo '  a[1]: ';
var_dump(ArtistCollection::getUnitOfWork()->containsObject($a[1]));
echo '  b[1]: ';
var_dump(ArtistCollection::getUnitOfWork()->containsObject($b[1]));

echo '  a[1]->albums[1]: ';
var_dump(ArtistCollection::getUnitOfWork()->containsObject($a[1]));
echo '  b[1]->albums[1]: ';
var_dump(ArtistCollection::getUnitOfWork()->containsObject($b[1]));

--EXPECT--
In Unit of Work: 
  a[1]: bool(true)
  b[1]: bool(false)
  a[1]->albums[1]: bool(true)
  b[1]->albums[1]: bool(false)