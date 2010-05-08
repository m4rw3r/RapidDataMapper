--TEST--
Test of the Rdm_Util_DescriptorLoader_SetUpMethod class
--FILE--
<?php

include 'config/config.php';

Rdm_Config::addDescriptorLoader(array(new Rdm_Util_DescriptorLoader_SetUpMethod(), 'load'));

class User
{
	public static function setUp(Rdm_Descriptor $desc)
	{
		$desc->setClass(__CLASS__);
		$desc->add($desc->newPrimaryKey('id'));
		$desc->add($desc->newColumn('name'));
	}
	
	public $id;
	public $name;
}

try
{
	Rdm_Config::getDescriptor('Foobar');
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

$desc = Rdm_Config::getDescriptor('User');

echo 'class: ';
var_dump($desc->getClass());

echo 'pks: ';
foreach($desc->getPrimaryKeys() as $c)
{
	var_dump($c->getProperty());
}

echo 'columns: ';
foreach($desc->getColumns() as $c)
{
	var_dump($c->getProperty());
}

--EXPECT--
string(52) "Descriptor for class "Foobar": Descriptor is missing"
class: string(4) "User"
pks: string(2) "id"
columns: string(4) "name"