--TEST--
Attempt persisting an already persisted obejct, no flush in between
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = new Artist();
$a->name = 'foobar';

ArtistCollection::persist($a);

ArtistCollection::persist($a);

echo "success";
--EXPECTREGEX--
success