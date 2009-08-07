<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * The main class to interact with when doing database queries.
 */
abstract class Db
{
	const ERROR = 1;
	const WARNING = 2;
	const NOTICE = 4;
	const DEBUG = 8;
	const ALL = 15;
	
	/**
	 * The path to this file, so the autoloader loads the file properly.
	 * 
	 * @var string
	 */
	protected static $lib_base = '';
	
	/**
	 * Associative of active database connections.
	 * 
	 * @var array
	 */
	protected static $conn_list = array();
	
	/**
	 * Associative array of connection configurations.
	 * 
	 * @var array
	 */
	protected static $conn_configs = array();
	
	/**
	 * Name of the default database connection to use.
	 * 
	 * @var string
	 */
	protected static $conn_default = 'default';
	
	/**
	 * Associative array of descriptors.
	 * 
	 * Value can be either an object or a class name.
	 * 
	 * @var array
	 */
	protected static $mapper_descriptors = array();
	
	/**
	 * Directory for containing the descriptors.
	 * 
	 * @var string
	 */
	protected static $mapper_desc_dir = '';
	
	/**
	 * Directory for containing the cached mappers.
	 * 
	 * @var string
	 */
	protected static $mapper_cache_dir = '';
	
	/**
	 * A list of attached loggers.
	 * 
	 * @var array
	 */
	protected static $loggers = array();
	
	// ------------------------------------------------------------------------

	/**
	 * Initializes the autoloader.
	 * 
	 * @return void
	 */
	public static function initAutoload()
	{
		self::$lib_base = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		
		spl_autoload_register(array('Db', 'autoload'));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Autoloads a certain class.
	 * 
	 * @param  string
	 * @return bool
	 */
	public static function autoload($class)
	{
		$file = self::$lib_base . str_replace(array('_', '/'), DIRECTORY_SEPARATOR, $class).'.php';
		
		if(file_exists($file))
		{
			require $file;
			
			if(class_exists($class, false))
			{
				return true;
			}
		}
		
		return false;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the configuration for a certain database configuration.
	 * 
	 * @param  string
	 * @param  array
	 * @return void
	 */
	public static function setConnectionConfig($name, $configuration = false)
	{
		if(is_array($name))
		{
			self::$conn_configs = array_merge(self::$conn_configs, $name);
		}
		elseif( ! $configuration)
		{
			throw new Db_Exception_InvalidConfiguration($name);
		}
		else
		{
			self::$conn_configs[$name] = $configuration;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Getter for the database connections, semi-singleton.
	 * 
	 * @param  string
	 * @return Db_Connection
	 */
	public static function getConnection($name = false)
	{
		if($name === false)
		{
			// use the default connection
			$name = self::$conn_default;
		}
		
		if( ! isset(self::$conn_list[$name]))
		{
			if(empty(self::$conn_configs[$name]) OR ! is_array(self::$conn_configs[$name]))
			{
				throw new Db_Exception_MissingConfig($name);
			}
			
			$class = 'Db_Driver_'.ucfirst(strtolower(self::$conn_configs[$name]['dbdriver'])).'_Connection';
			
			self::$conn_list[$name] = new $class($name, self::$conn_configs[$name]);
		}
		
		return self::$conn_list;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns all initialized connections.
	 * 
	 * @return array
	 */
	public static function getLoadedConnection()
	{
		return self::$conn_list;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the directory for the mapper cache.
	 * 
	 * @return 
	 */
	public static function setMapperCacheDir($path)
	{
		self::$mapper_cache_dir = $path;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets a directory which contains the mapper descriptors.
	 * 
	 * @param  string
	 * @return void
	 */
	public static function setDescriptorDirectory($directory)
	{
		self::$mapper_desc_dir = $directory;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets a certain descriptor for a specified class
	 * 
	 * @param  string
	 * @param  Db_Descriptor
	 * @return void
	 */
	public static function setDescriptor($class, Db_Descriptor $descriptor)
	{
		self::$mapper_descriptors[strtolower($class)] = $descriptor;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the mapper instance for the supplied class.
	 * 
	 * @param  string
	 * @return Db_Mapper
	 */
	public static function getMapper($class)
	{
		
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Logs an event.
	 * 
	 * @param  int		Error level
	 * @param  string
	 * @return void
	 */
	public static function log($level, $message)
	{
		foreach(self::$loggers as $logger)
		{
			call_user_func($logger, $level, $message);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a callable which will receive the log messages ($level, $message).
	 * 
	 * @param  callable
	 * @return void
	 */
	public function attach_logger($callable)
	{
		self::$loggers[] = $callable;
	}
}


/* End of file Db.php */
/* Location: . */