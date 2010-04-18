<?php
/*
 * Created by Martin Wernståhl on 2010-04-01.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

// Just to make sure that we don't miss any errors
error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

// Register RapidDataMapper's default autoloader implementation
require 'lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();


// Initialize <Class>Collection autoloaders, do not auto call Rdm_Collection::flush()
Rdm_Collection::init(false);


// Configure RapidDataMapper Adapter
Rdm_Config::setAdapterConfiguration('default', array(
	'hostname' => 'localhost',
	'username' => 'ci',
	'password' => '',
	'database' => 'rdmtest',
	'class' => 'Rdm_Adapter_MySQL'
	));

/* End of file config.php */
/* Location: ./phpt */