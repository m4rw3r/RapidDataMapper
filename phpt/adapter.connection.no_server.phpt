--TEST--
Try to connect to a non-existing server
--FILE--
<?php

include 'config/config.php';

Rdm_Config::setAdapterConfiguration('default', array('class' => 'Rdm_Adapter_MySQL', 'username' => 'dummy', 'hostname' => '999.999.999', 'password' => '', 'database' => 'foobar'));

var_dump(Rdm_Adapter::getInstance()->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
--EXPECTREGEX--
Fatal error: Uncaught exception 'Rdm_Adapter_ConnectionException' with message 'Connection Error: "php_network_getaddresses: getaddrinfo failed: nodename nor servname provided, or not known"\.'.*