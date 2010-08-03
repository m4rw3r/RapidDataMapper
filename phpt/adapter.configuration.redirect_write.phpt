--TEST--
Try to redirect SQLite writes to a MySQL adapter using Rdm_Adapter::setWriteAdapter()
--FILE--
<?php

include 'config/config.php';
include 'fixtures/ArtistAlbumTrack.php';

$db1 = Config::getAdapter();
$db2 = new Rdm_Adapter_SQLite(array('file' => ':memory:', 'redirect_write' => $db1));

echo '$db1.artists[1]: ';
try
{
	var_dump($db1->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}
echo '$db2.artists[1]: ';
try
{
	var_dump(@$db2->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

$lol = true;

echo '$db2.artists[3] = new: ';
try
{
	var_dump(@$db2->query('INSERT INTO tbl_artists (name) VALUES (\'Dark Tranquillity\')'));
}
catch(Rdm_Exception $e)
{
	throw $e;
	var_dump($e->getMessage());
}

echo '$db1.artists[3]: ';
try
{
	var_dump($db1->query('SELECT name FROM tbl_artists WHERE id = 3')->val());
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}
echo '$db2.artists[3]: ';
try
{
	var_dump(@$db2->query('SELECT name FROM tbl_artists WHERE id = 3')->val());
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

--EXPECT--
$db1.artists[1]: string(9) "Draconian"
$db2.artists[1]: string(129) "Query error: 1, SQL logic error or missing database: no such table: tbl_artists, SQL: "SELECT name FROM tbl_artists WHERE id = 1""
$db2.artists[3] = new: int(1)
$db1.artists[3]: string(17) "Dark Tranquillity"
$db2.artists[3]: string(129) "Query error: 1, SQL logic error or missing database: no such table: tbl_artists, SQL: "SELECT name FROM tbl_artists WHERE id = 3""