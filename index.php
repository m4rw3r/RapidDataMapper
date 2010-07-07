<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

namespace Foo;

use \Rdm_Descriptor, \Rdm_Adapter, \Rdm_CollectionManager;

// Make sure that the browser reads the response properly, we're using UTF-8 in the DB
header('Content-type: text/html;Charset=UTF-8');

echo "<pre>";

require 'config.php';

class TrackDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add($this->newPrimaryKey('id'));
		
		$this->add($this->newColumn('name'));
		$this->add($this->newColumn('artist_id')->setDataType(self::INT));
		$this->add($this->newColumn('album_id')->setDataType(self::INT));
		$this->add($this->newRelation('artist'));
		$this->add($this->newRelation('album'));
	}
}

class ArtistDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add($this->newPrimaryKey('id'));
		
		$this->add($this->newColumn('name'));
		$this->add($this->newRelation('tracks'));
		$this->add($this->newRelation('albums'));
	}
}

class AlbumDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add($this->newPrimaryKey('id'));
		
		$this->add($this->newColumn('name'));
		$this->add($this->newColumn('artist_id'));
		$this->add($this->newRelation('tracks'));
		$this->add($this->newRelation('artist'));
	}
}

class Track
{
	public $id;
	public $name;
	public $artist_id;
	public $album_id;
	
	public function setId($id)
	{
		$this->id = $id;
	}
}

class Artist
{
	public $id;
	public $name;
}

class Album
{
	public $id;
	public $name;
	public $artist_id;
}

$db = Rdm_Adapter::getInstance();
var_dump($db->transactionStart());

new ArtistCollection;

$a = new Artist;

ArtistTracksRelation::establish($a, $t = new Track);

var_dump($a);
var_dump($t);

$artists = ArtistCollection::create()
  ->with(ArtistCollection::Albums)
    ->with(AlbumCollection::Tracks)
    ->end()
  ->end();

foreach($artists as $a)
{
	echo "$a->name\n";
	
	foreach($a->albums as $al)
	{
		echo "  $al->name\n";
		
		foreach($al->tracks as $t)
		{
			echo "    $t->name\n";
		}
	}
}

Rdm_CollectionManager::pushChanges();

$db->transactionRollback();

foreach(Rdm_Adapter::getAllInstances() as $i)
{
	echo "\n\nQueries from adapter with name {$i->getName()}:\n\n";
	
	foreach($i->queries as $q)
	{
		echo $q['time']."\n".$q['sql']."\n\n";
	}
}

echo count(get_included_files());

print_r(get_included_files());


/*
foreach(TrackCollection::create()
	->with(TrackCollection::Artist)
	->end()
	->with(TrackCollection::Album)
	->end() as $r)
{
	var_dump($r);
}


for($i = 0; $i < 1000; $i++)
{
	foreach(TrackCollection::create()
		->with(TrackCollection::Artist)
		->end()
		->with(TrackCollection::Album)
		->end() as $r)
	{
		
	}
}

/*

for($i = 0; $i < 1000; $i++)
{
	$c = TrackCollection::create()
		->with(TrackCollection::Artist)
			->has()
				->name('goo')
			->end()
		->end()
		->has()
			->name('foobar')
		->end();
	
	$a = (String) $c;
}

/* End of file index.php */
/* Location: . */