--TEST--
Test of the Serialize datatype
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
		$desc->add($desc->newColumn('data')->setDataType(Rdm_Descriptor::SERIALIZE));
	}
	
	public $id;
	public $data = null;
}

Config::getAdapter()->query('CREATE TABLE "tbl_data_entries" (
	"id" INTEGER PRIMARY KEY,
	"data" TEXT
	)');

$e  = new DataEntry();
$e2 = new DataEntry();
$e2->data = new DataEntry();
$e2->data->data = array('Foo', 'Bar');

DataEntryCollection::persist($e);
DataEntryCollection::persist($e2);

Config::getManager()->pushChanges();

var_dump(Config::getAdapter()->query('SELECT data FROM tbl_data_entries WHERE id = 1')->val());
var_dump(Config::getAdapter()->query('SELECT data FROM tbl_data_entries WHERE id = 2')->val());

// Insert new row to fetch
Config::getAdapter()->query('INSERT INTO tbl_data_entries (data) VALUES (\'O:9:"DataEntry":2:{s:2:"id";N;s:4:"data";s:3:"lol";}\')');

print_r(DataEntryCollection::fetchByPrimaryKey(3)->data);

--EXPECT--
string(2) "N;"
string(76) "O:9:"DataEntry":2:{s:2:"id";N;s:4:"data";a:2:{i:0;s:3:"Foo";i:1;s:3:"Bar";}}"
DataEntry Object
(
    [id] => 
    [data] => lol
)