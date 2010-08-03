--TEST--
Try to clone, serialize and unserialize Rdm_Adapter and Rdm_Adapter_Result
--FILE--
<?php

include 'config/config.php';
include 'fixtures/ArtistAlbumTrack.php';

try
{
	$clone = clone Config::getAdapter();
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

try
{
	$serialized = serialize(Config::getAdapter());
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

try
{
	$class_name = 'Rdm_Adapter_SQLite';
	$clone = unserialize('O:' . strlen($class_name) . ':"' .$class_name. '":0:{}');
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

$r = Config::getAdapter()->query('SELECT * FROM tbl_tracks WHERE id = 1');

try
{
	$clone = clone $r;
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

try
{
	$serialized = serialize($r);
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

try
{
	$class_name = 'Rdm_Adapter_SQLite_Result';
	$clone = unserialize('O:' . strlen($class_name) . ':"' .$class_name. '":0:{}');
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}
--EXPECT--
string(46) "Cloning of Rdm_Adapter objects is not allowed."
string(52) "Serialization of Rdm_Adapter objects is not allowed."
string(54) "Unserialization of Rdm_Adapter objects are is allowed."
string(53) "Cloning of Rdm_Adapter_Result objects is not allowed."
string(59) "Serialization of Rdm_Adapter_Result objects is not allowed."
string(61) "Unserialization of Rdm_Adapter_Result objects are is allowed."