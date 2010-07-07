--TEST--
Try to configure an adapter with missing required options
--FILE--
<?php

include 'config/config.php';

$db = new Rdm_Adapter_MySQL(array());

--EXPECTREGEX--
Fatal error: Uncaught exception 'Rdm_Adapter_ConfigurationException' with message 'The supplied adapter configuration is missing required keys: "hostname", "username", "password", "database"'.*