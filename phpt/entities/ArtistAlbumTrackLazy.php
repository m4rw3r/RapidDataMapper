<?php

// A variant of the ArtistAlbumTrack.php file, but with lazy loading built in

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
	public function getArtist()
	{
		if(empty($this->artist) && $this->artist_id)
		{
			$this->artist = ArtistCollection::fetchByRelatedTracks($this);
		}
		
		return $this->artist;
	}
	public function getAlbum()
	{
		if(empty($this->album) && $this->album_id)
		{
			$this->album = AlbumCollection::fetchByRelatedTracks($this);
		}
		
		return $this->album;
	}
}

class Artist
{
	public $id;
	public $name;
	public $albums;
	public $tracks;
	public function __construct()
	{
		$this->albums = AlbumCollection::createFromArtist($this);
		$this->tracks = TrackCollection::createFromArtist($this);
	}
}

class Album
{
	public $id;
	public $name;
	public $artist_id;
	public $tracks;
	public $artist;
	public function __construct()
	{
		$this->tracks = TrackCollection::createFromAlbum($this);
	}
	public function getArtist()
	{
		if(empty($this->artist) && $this->artist_id)
		{
			$this->artist = ArtistCollection::fetchByRelatedAlbums($this);
		}
		
		return $this->artist;
	}
}