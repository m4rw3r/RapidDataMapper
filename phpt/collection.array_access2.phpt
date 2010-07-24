--TEST--
offsetIsset() of the ArrayAccess interface
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$ac = ArtistCollection::create();

echo 'ac[0]: ';
var_dump(isset($ac[0]));
echo 'ac[1]: ';
var_dump(isset($ac[1]));
echo 'ac[2]: ';
var_dump(isset($ac[2]));
echo 'ac[3]: ';
var_dump(isset($ac[3]));

--EXPECT--
ac[0]: bool(false)
ac[1]: bool(true)
ac[2]: bool(true)
ac[3]: bool(false)
