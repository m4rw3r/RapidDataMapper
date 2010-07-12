--TEST--
Pass wrong obejct type to <Entity>Collection->remove()
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$artists = ArtistCollection::create()
	->with(ArtistCollection::Albums)
	->end()
	->has()->id(1)->end();

$artist = $artists[1];

try
{
	$artist->albums->remove($artist);
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

--EXPECT--
string(35) "Object of type \Album was expected."