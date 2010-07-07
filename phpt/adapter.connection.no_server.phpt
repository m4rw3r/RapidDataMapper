--TEST--
Try to connect to a non-existing server
--FILE--
<?php

include 'config/config.php';

$db = new Rdm_Adapter_MySQL(array('username' => 'dummy', 'hostname' => '999.999.999', 'password' => '', 'database' => 'foobar'));

var_dump($db->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
--EXPECTREGEX--
Fatal error: Uncaught exception 'Rdm_Adapter_ConnectionException' with message 'Could not connect to host "999.999.999": 2002, php_network_getaddresses: getaddrinfo failed: nodename nor servname provided, or not known'.*