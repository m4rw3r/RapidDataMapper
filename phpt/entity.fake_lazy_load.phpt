--TEST--
Fakes lazy loading of entities by using internal collections created by the entities' constructors
--FILE--
<?php

include 'config/config.php';
include 'entities/ArtistAlbumTrackLazy.php';
include 'fixtures/ArtistAlbumTrack.php';

$a = ArtistCollection::fetchByPrimaryKey(1);

foreach($a->tracks as $t)
{
	echo "$t->name\n";
}

echo "===\n";

foreach($a->albums as $al)
{
	echo "$al->name\n";
}

echo "===\n";

var_dump($a->albums[1]->getArtist()->name);
var_dump($a->albums[1]->tracks[1]->getArtist()->name);

--EXPECT--
Seasons Apart
When I Wake
Earthbound
Not Breathing
The Failure Epiphany
Morphine Cloud
Bloodflower
The Empty Stare
September Ashes
The Cry of Silence
Silent Winter
A Slumber Did My Spirit Seal
The Solitude
Reversio Ad Secessum
The Amaranth
Akherousia
It Grieves My Heart
===
Turning Season Within
Where Lovers Mourn
===
string(9) "Draconian"
string(9) "Draconian"