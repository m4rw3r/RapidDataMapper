--TEST--
Use the annotation descriptor loader to create the descriptors
--FILE--
<?php

include 'config/config.php';
include 'fixtures/ArtistAlbumTrack.php';

Config::getConfig()->addDescriptorLoader(new Rdm_Util_DescriptorLoader_Annotation());

class Track
{
	#[PrimaryKey]
	public $id;
	#[Column]
	/**
	 * Test, this should not affect the annotation parser
	 */
	public $name;
	#[Column(type=INT)]
	public $artist_id;
	/**
	 * Test, this should not affect the annotation parser
	 */
	#[Column(type=INT)]
	public $album_id;
	#[Relation]
	public $artist;
	#[Relation]
	public $albums;
}

/**
 * Test, this should not affect the annotation parser
 */
class Artist
{
	/**
	 * [Test(should=not,affect=parser)]
	 */
	#[PrimaryKey]
	public $id;
	#[Column]
	public $name;
	#[Relation]
	public $tracks = array();
	#[Relation]
	public $albums = array();
}

class Album
{
    #[PrimaryKey]
    public $id;
    #[Column]
    public $name;
    #[Column(type=INT)]
    public $artist_id;
    #[Relation]
    public $artist;
    #[Relation]
    public $tracks = array();
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
