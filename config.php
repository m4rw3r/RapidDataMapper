<?php
/*
 * Created by Martin Wernståhl on 2010-04-01.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

// Register RapidDataMapper's default autoloader implementation
require 'lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();

Rdm_Collection::init();

// Register the example loader
spl_autoload_register('exampleloader');
function exampleloader($class)
{
	require ltrim(strtr($class, '\\_', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR).'.php', DIRECTORY_SEPARATOR);
}

// Configure RapidDataMapper
Rdm_Config::setAdapterConfiguration('default', array(
	'hostname' => 'localhost',
	'username' => 'ci',
	'password' => '',
	'database' => 'test',
	'class' => 'Rdm_Adapter_MySQL'
	));

/* End of file config.php */
/* Location: . */