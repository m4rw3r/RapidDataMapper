<?php
/*
 * Created by Martin Wernståhl on 2010-04-01.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

// Just to make sure that we don't miss any errors
error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

// Register RapidDataMapper's default autoloader implementation
require dirname(__FILE__).'/../../lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();

/**
 * Service container which provides the basic instances for the tests.
 */
class Config
{
	protected static $adapter;
	
	protected static $config;
	
	protected static $manager;
	
	public static function init()
	{
		static $run = false;
		
		if($run)
		{
			return;
		}
		
		$run = true;
		
		// Configure RapidDataMapper Adapter
		self::$adapter = new Rdm_Adapter_SqLite(array(
			'file'     => ':memory:',
			'dbprefix' => 'tbl_'
			));

		// Configure the mapper configuration
		self::$config = new Rdm_Config();
		self::$config->setAdapter(self::$adapter);

		// Initialize <Class>Collection autoloaders, do not auto call Rdm_CollectionManager::pushChanges()
		self::$manager = new Rdm_CollectionManager(self::$config);
		self::$manager->registerCollectionAutoloader(false);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the configured database adapter.
	 * 
	 * @return Rdm_Adapter
	 */
	public static function getAdapter()
	{
		return self::$adapter;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the configuration object.
	 * 
	 * @return Rdm_Config
	 */
	public static function getConfig()
	{
		return self::$config;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the CollectionManager instance for this configuration.
	 * 
	 * @return Rdm_CollectionManager
	 */
	public static function getManager()
	{
		return self::$manager;
	}
}

Config::init();

/* End of file config.php */
/* Location: ./phpt */