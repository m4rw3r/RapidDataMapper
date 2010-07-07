<?php
/*
 * Created by Martin Wernståhl on 2010-04-01.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

// Just to make sure that we don't miss any errors
error_reporting(E_ALL | E_STRICT | E_DEPRECATED);


// Mapper cache directory
$mapper_cache_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'cache';


// Remove all the cached mappers, this to make sure that we always get "fresh" files
foreach(glob($mapper_cache_dir.DIRECTORY_SEPARATOR.'*.php') as $f)
{
	@unlink($f);
}


// Register RapidDataMapper's default autoloader implementation
require 'lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();


// Initialize <Class>Collection autoloaders
Rdm_CollectionManager::init();


// Register the example loader which loads files using a normal autoloader
spl_autoload_register('exampleloader');
function exampleloader($class)
{
	require ltrim(strtr($class, '\\_', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR).'.php', DIRECTORY_SEPARATOR);
}


// Configure RapidDataMapper Adapter
Rdm_Config::setAdapterConfiguration('default', array(
	'hostname' => 'localhost',
	'username' => 'ci',
	'password' => '',
	'database' => 'test',
	'class' => 'Rdm_Adapter_MySQL'
	));


// Generated file storage configuration
Rdm_Config::setCacheMappers(true);
Rdm_Config::setMapperCacheDir($mapper_cache_dir);

/* End of file config.php */
/* Location: . */