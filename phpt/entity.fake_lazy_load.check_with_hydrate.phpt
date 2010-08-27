--TEST--
Fakes lazy loading of entities by using internal collections created by the entities' constructors, check so that these objects doesn't interfere with lazy loading
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrackLazy.php';
include 'fixtures/ArtistAlbumTrack.php';

$artists = ArtistCollection::create()
  ->with(ArtistCollection::Albums)
  ->with(AlbumCollection::Tracks)
  ->end()->end();

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

echo "===\n";

$al = new Album();
$al->name = 'Foobar';

$artists[1]->albums[] = $al;

echo 'al->artist_id: ';
var_dump($al->artist_id);

--EXPECT--
Draconian
  Turning Season Within
    Seasons Apart
    When I Wake
    Earthbound
    Not Breathing
    The Failure Epiphany
    Morphine Cloud
    Bloodflower
    The Empty Stare
    September Ashes
  Where Lovers Mourn
    The Cry of Silence
    Silent Winter
    A Slumber Did My Spirit Seal
    The Solitude
    Reversio Ad Secessum
    The Amaranth
    Akherousia
    It Grieves My Heart
Cult of Luna
  Eternal Kingdom
    Owlwood
    Eternal Kingdom
    Ghost Trail
    The Lure (Interlude)
    Mire Deep
    The Great Migration
    Österbotten
    Curse
    Ugin
    Following Betulas
===
al->artist_id: int(1)