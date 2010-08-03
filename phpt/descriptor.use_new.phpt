--TEST--
Add primary keys, columns and relations using the "new" keyword
--FILE--
<?php

include 'config/config.php';
include 'fixtures/ArtistAlbumTrack.php';

// For shorthand:
use Rdm_Descriptor_PrimaryKey as PrimaryKey;
use Rdm_Descriptor_Column as Column;
use Rdm_Descriptor_Relation as Relation;

class TrackDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add(new PrimaryKey('id'));
		
		$this->add(new Column('name'));
		$this->add(new Column('artist_id', self::INT));
		$this->add(new Column('album_id', self::INT));
		$this->add(new Relation('artist'));
		$this->add(new Relation('album'));
	}
}

class ArtistDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add(new PrimaryKey('id'));
		
		$this->add(new Column('name'));
		$this->add(new Relation('tracks'));
		$this->add(new Relation('albums'));
	}
}

class AlbumDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add(new PrimaryKey('id'));
		
		$this->add(new Column('name'));
		$this->add(new Column('artist_id', self::INT));
		
		$this->add(new Relation('tracks'));
		$this->add(new Relation('artist'));
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

$artists = ArtistCollection::create()
	->with(ArtistCollection::Albums)
		->with(AlbumCollection::Tracks)
		->end()
	->end();

foreach($artists as $a)
{
    echo "$a->id  $a->name\n";
    
    foreach($a->albums as $al)
    {
        echo "  $al->id  $al->name\n";
        
        foreach($al->tracks as $t)
        {
            echo "    $t->id  $t->name\n";
        }
    }
}
--EXPECT--
1  Draconian
  1  Turning Season Within
    1  Seasons Apart
    2  When I Wake
    3  Earthbound
    4  Not Breathing
    5  The Failure Epiphany
    6  Morphine Cloud
    7  Bloodflower
    8  The Empty Stare
    9  September Ashes
  2  Where Lovers Mourn
    20  The Cry of Silence
    21  Silent Winter
    22  A Slumber Did My Spirit Seal
    23  The Solitude
    24  Reversio Ad Secessum
    25  The Amaranth
    26  Akherousia
    27  It Grieves My Heart
2  Cult of Luna
  3  Eternal Kingdom
    10  Owlwood
    11  Eternal Kingdom
    12  Ghost Trail
    13  The Lure (Interlude)
    14  Mire Deep
    15  The Great Migration
    16  Österbotten
    17  Curse
    18  Ugin
    19  Following Betulas
