--TEST--
Test serialization of collection objects and their ties to the Unit Of Work
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = new ArtistCollection();

$s = serialize($a);

$b = unserialize($s);

print_r($a[1]);
echo "\n";
print_r($b[1]);

echo "In Unit of Work: \n";
echo '  a: ';
var_dump(ArtistCollection::getUnitOfWork()->containsObject($a[1]));
echo '  b: ';
var_dump(ArtistCollection::getUnitOfWork()->containsObject($b[1]));

--EXPECT--
Artist Object
(
    [id] => 1
    [name] => Draconian
    [__id] => Array
        (
            [id] => 1
        )

    [__data] => Array
        (
            [name] => Draconian
        )

)

Artist Object
(
    [id] => 1
    [name] => Draconian
    [__id] => Array
        (
            [id] => 1
        )

    [__data] => Array
        (
            [name] => Draconian
        )

)
In Unit of Work: 
  a: bool(true)
  b: bool(false)