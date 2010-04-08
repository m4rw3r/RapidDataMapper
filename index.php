<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

echo "<pre>";

require 'config.php';

class TrackDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add($this->newPrimaryKey('id'));
		
		$this->add($this->newColumn('name'));
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
	}
}

class AlbumDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add($this->newPrimaryKey('id'));
		
		$this->add($this->newColumn('name'));
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
}


foreach(ArtistCollection::create()
	->with(ArtistCollection::Tracks)
	->end() as $a)
{
	echo "$a->name\n";
	
	foreach($a->tracks as $t)
	{
		echo "    $t->name\n";
	}
}

foreach(Rdm_Adapter::getAllInstances() as $i)
{
	echo "\n\nQueries from adapter with name {$i->getName()}:\n\n";
	
	foreach($i->queries as $time_id => $query)
	{
		echo $i->query_times[$time_id]."\n".$query."\n\n";
	}
}

/*
foreach(TrackCollection::create()
	->with(TrackCollection::Artist)
	->end()
	->with(TrackCollection::Album)
	->end() as $r)
{
	var_dump($r);
}

/*
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