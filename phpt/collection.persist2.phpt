--TEST--
Attempt persisting an already persisted obejct
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = new Artist();
$a->name = 'foobar';

ArtistCollection::persist($a);

Rdm_CollectionManager::pushChanges();

ArtistCollection::persist($a);
--EXPECTREGEX--
Fatal error: Uncaught exception 'Rdm_UnitOfWork_Exception' with message 'The object of type Artist is already being persisted.'.*