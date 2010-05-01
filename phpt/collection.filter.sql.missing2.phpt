--TEST--
Use TrackCollectionFilter->sql() using multiple bound parameters, skip a required parameter, using ":len" placeholder
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrack.php';
include 'fixtures/ArtistAlbumTrack.php';

$tracks = TrackCollection::create()
	->with(TrackCollection::Album)
		->has()
		->sql('LENGTH(:alias.name) < :len OR SUBSTR(:alias.name, 0, 1) = :firstchar',
			array('firstchar' => 'W'))
		->end()
	->end();

foreach($tracks as $t)
{
	echo "$t->id  $t->name\n";
}
--EXPECTREGEX--
Fatal error: Uncaught exception 'Rdm_Adapter_MissingBoundParameterException' with message 'Missing Bound Parameter "len"'.*