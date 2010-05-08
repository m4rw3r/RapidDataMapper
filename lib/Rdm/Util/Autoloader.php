<?php
/*
 * Created by Martin Wernståhl on 2010-04-01.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Utility class which handles the default RapidDataMapper class autoloading,
 * use if you do not have a compatible autoloader, see manual.
 */
final class Rdm_Util_Autoloader
{
	/**
	 * The directory containing the RapidDataMapper library files.
	 * 
	 * @var string
	 */
	private static $library_dir;
	
	/**
	 * Private constructor to prevent instantiation.
	 */
	private final function __construct()
	{
		// Empty
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Registers the autoloader
	 * 
	 * @return void
	 */
	public static function init()
	{
		spl_autoload_register(__CLASS__.'::autoload');
		
		self::$library_dir = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Loads a RapidDataMapper class file (ie. a classname which starts with Rdm\).
	 * 
	 * @param  string
	 * @return boolean
	 */
	public static function autoload($class)
	{
		if(strpos($class, 'Rdm_') !== 0)
		{
			return false;
		}
		
		$file = self::$library_dir.strtr(substr($class, 4), '_', DIRECTORY_SEPARATOR).'.php';
		
		if(file_exists($file))
		{
			require $file;
			
			return true;
		}
	}
}


/* End of file Autoloader.php */
/* Location: ./lib/Rdm/Util */