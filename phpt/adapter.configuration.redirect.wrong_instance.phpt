--TEST--
Try to redirect SQLite writes to a MySQL adapter
--FILE--
<?php

include 'config/config.php';

$db = new Rdm_Adapter_MySQL(array('username' => 'dummy', 'hostname' => '999.999.999', 'password' => '', 'database' => 'foobar', 'redirect_write' => Config::getAdapter()));

--EXPECTF--
Fatal error: Uncaught exception 'Rdm_Adapter_ConfigurationException' with message 'The redirect_write key of the configuration is of the wrong type (Rdm_Adapter_SQLite) instead of an Rdm_Adapter_MySQL instance.'%s