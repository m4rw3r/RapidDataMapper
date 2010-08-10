--TEST--
Test of the Allow NULL, and NOT NULL of the INT datatype
--FILE--
<?php

include 'config/config.php';

$c = Config::getConfig();

$c->addDescriptorLoader(array(new Rdm_Util_DescriptorLoader_SetUpMethod(), 'load'));

class DataEntry
{
	public static function setUp(Rdm_Descriptor $desc)
	{
		$desc->setClass(__CLASS__);
		$desc->setTable('data_entries');
		$desc->add($desc->newPrimaryKey('id'));
		
		// Don't allow null here:
		$desc->add($desc->newColumn('id1')->setDataType(Rdm_Descriptor::INT, 11, false));
		
		// Allow null:
		$desc->add($desc->newColumn('id2')->setDataType(Rdm_Descriptor::INT, 11, true));
	}
	
	public $id;
	public $id1 = null;
	public $id2 = null;
}

Config::getAdapter()->query('CREATE TABLE "tbl_data_entries" (
	"id" INTEGER PRIMARY KEY,
	"id1" INT(11) NOT NULL,
	"id2" INT(11) DEFAULT NULL
	)');

$e  = new DataEntry();
$e->id1 = null;
$e->id2 = null;

DataEntryCollection::persist($e);

Config::getManager()->pushChanges();

var_dump(Config::getAdapter()->query('SELECT id1 FROM tbl_data_entries WHERE id = 1')->val());
var_dump(Config::getAdapter()->query('SELECT id2 FROM tbl_data_entries WHERE id = 1')->val());

// Insert new row to fetch
Config::getAdapter()->query('INSERT INTO tbl_data_entries (id1, id2) VALUES (0, 0)');

var_dump(DataEntryCollection::fetchByPrimaryKey(2));

Config::getAdapter()->query('INSERT INTO tbl_data_entries (id1, id2) VALUES (0, NULL)');

var_dump(DataEntryCollection::fetchByPrimaryKey(3));

--EXPECTF--
string(1) "0"
NULL
object(DataEntry)#%d (5) {
  ["id"]=>
  int(2)
  ["id1"]=>
  int(0)
  ["id2"]=>
  int(0)
  ["__id"]=>
  array(1) {
    ["id"]=>
    int(2)
  }
  ["__data"]=>
  array(2) {
    ["id1"]=>
    int(0)
    ["id2"]=>
    int(0)
  }
}
object(DataEntry)#%d (5) {
  ["id"]=>
  int(3)
  ["id1"]=>
  int(0)
  ["id2"]=>
  NULL
  ["__id"]=>
  array(1) {
    ["id"]=>
    int(3)
  }
  ["__data"]=>
  array(2) {
    ["id1"]=>
    int(0)
    ["id2"]=>
    NULL
  }
}