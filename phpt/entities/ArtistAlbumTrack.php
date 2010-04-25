<?php

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
		$this->add($this->newColumn('artist_id')->setDataType(self::INT));
		
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