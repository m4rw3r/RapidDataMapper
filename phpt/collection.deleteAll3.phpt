--TEST--
Delete a select number of tracks using has()->artist_id(1)->end()->deleteAll();, NO PUSH
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = TrackCollection::create()->has()->artist_id(1)->end();

$a->deleteAll();

echo 'Draconian: ';
var_dump(Config::getAdapter()->query('SELECT COUNT(1) FROM tbl_tracks WHERE artist_id = 1')->val());
echo 'Cult Of Luna: ';
var_dump(Config::getAdapter()->query('SELECT COUNT(1) FROM tbl_tracks WHERE artist_id = 2')->val());
--EXPECT--
Draconian: string(2) "17"
Cult Of Luna: string(2) "10"