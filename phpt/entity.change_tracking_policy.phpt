--TEST--
Switches the change tracking policy to EXPLICIT mode
--FILE--
<?php

include 'config/config.php';
include 'fixtures/ArtistAlbumTrack.php';

class Track
{
	public $id;
	public $name;
}

class TrackDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add($this->newPrimaryKey('id'));
		
		$this->add($this->newColumn('name'));
		
		$this->setChangeTrackingPolicy(self::EXPLICIT);
	}
}

var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 1')->val());
var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 2')->val());
var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 3')->val());
var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 4')->val());

echo "===\n";

$t1 = TrackCollection::fetchByPrimaryKey(1);
$t2 = TrackCollection::fetchByPrimaryKey(2);
$t3 = TrackCollection::fetchByPrimaryKey(3);
$t4 = TrackCollection::fetchByPrimaryKey(4);

$t1->name = 'foo1';
$t2->name = 'foo2';
$t3->name = 'foo3';
$t4->name = 'foo4';

Config::getManager()->pushChanges();

var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 1')->val());
var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 2')->val());
var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 3')->val());
var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 4')->val());

echo "===\n";

TrackCollection::markChanged($t1);
TrackCollection::markChanged($t2);

Config::getManager()->pushChanges();

var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 1')->val());
var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 2')->val());
var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 3')->val());
var_dump(Config::getAdapter()->query('SELECT name FROM tbl_tracks WHERE id = 4')->val());
--EXPECT--
string(13) "Seasons Apart"
string(11) "When I Wake"
string(10) "Earthbound"
string(13) "Not Breathing"
===
string(13) "Seasons Apart"
string(11) "When I Wake"
string(10) "Earthbound"
string(13) "Not Breathing"
===
string(4) "foo1"
string(4) "foo2"
string(10) "Earthbound"
string(13) "Not Breathing"