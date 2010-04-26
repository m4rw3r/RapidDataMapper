--TEST--
Use TrackCollectionFilter->sql() using multiple bound parameters, skip a required parameter, using "?" placeholder
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()
	->has()
		->sql(':alias.name LIKE ? OR :alias.name LIKE ?',
		array('%o_l%'))
	->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}
--EXPECTREGEX--
Fatal error: Uncaught exception 'Rdm_Adapter_MissingBoundParameterException' with message 'Missing Bound Parameter "2"'.*