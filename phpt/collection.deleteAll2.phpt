--TEST--
Delete a select number of tracks using has()->artist_id(1)->end()->deleteAll();
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = TrackCollection::create()->has()->artist_id(1)->end();

$a->deleteAll();

Config::getManager()->pushChanges();

echo 'Draconian: ';
var_dump(Config::getAdapter()->query('SELECT COUNT(1) FROM tbl_tracks WHERE artist_id = 1')->val());
echo 'Cult Of Luna: ';
var_dump(Config::getAdapter()->query('SELECT COUNT(1) FROM tbl_tracks WHERE artist_id = 2')->val());
--EXPECT--
Draconian: string(1) "0"
Cult Of Luna: string(2) "10"