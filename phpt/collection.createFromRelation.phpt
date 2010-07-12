--TEST--
Use the <Entity>Collection::createFrom<Relation>() shortcut to create a collection which filters tracks by related artist
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = ArtistCollection::fetchByPrimaryKey(1);

$tracks = TrackCollection::createFromArtist($a);

var_dump(count($tracks));
--EXPECT--
int(17)