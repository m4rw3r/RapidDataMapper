--TEST--
Try to configure an adapter with missing required options
--FILE--
<?php

include 'config/config.php';

Rdm_Config::setAdapterConfiguration('default', array('class' => 'Rdm_Adapter_MySQL'));

var_dump(Rdm_Adapter::getInstance()->query('SELECT name FROM tbl_artists WHERE id = 1')->val());
--EXPECTREGEX--
Fatal error: Uncaught exception 'Rdm_Adapter_ConfigurationException' with message 'Rdm_Adapter configuration with name "default": Missing required keys: "hostname", "username", "password", "database".'.*