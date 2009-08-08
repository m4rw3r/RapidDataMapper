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
	 * Associative array of loaded mappers.
	 * 
	 * @var array	class_name => Db_Mapper object
	 */
	protected static $mapper_list = array();
	
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
			
			// did we get a class or interface? (do not try to autoload)
			if(class_exists($class, false) OR interface_exists($class, false))
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
		
		return self::$conn_list[$name];
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
	 * Sets a directory which contains the mapper descriptors, they are loaded upon request.
	 * 
	 * The descriptor file names must be named like this: ClassName.php
	 * And the classes must be named like this: ClassNameDescriptor
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
	 * Adds a certain descriptor.
	 * 
	 * @param  Db_Descriptor
	 * @return void
	 */
	public static function addDescriptor(Db_Descriptor $descriptor)
	{
		self::$mapper_descriptors[$descriptor->getClass()] = $descriptor;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the descriptor for a certain class.
	 * 
	 * - First checks if there already is a loaded
	 *   (or manually loaded, via addDescriptor()).
	 * - Then it checks if ClassNameDescriptor.php exists
	 *   (it uses the autoloader, so Record_UserDescriptor will be placed in Record/UserDescriptor.php).
	 * - Finally it tries the descriptor directory for any files with the name
	 *   ClassName.php, which will contain a ClassNameDescriptor class.
	 * 
	 * @param  string
	 * @throws Db_Exception_MissingDescriptor
	 * @return Db_Descriptor
	 */
	public static function getDescriptor($class)
	{
		if(isset(self::$mapper_descriptors[$class]))
		{
			return self::$mapper_descriptors[$class];
		}
		
		// default class name
		$klass = $class.'Descriptor';
		
		// do we have a descriptor class? (it also tries to autoload it with class_exists())
		if( ! class_exists($klass))
		{
			// do we have a certain descriptor file?
			if(file_exists(self::$mapper_desc_dir.'/'.$class.'.php'))
			{
				require self::$mapper_desc_dir.'/'.$class.'.php';
			}
			else
			{
				throw new Db_Exception_MissingDescriptor($class);
			}
		}
		else
		{
			return self::$mapper_descriptors[$class] = new $klass();
		}
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
		if(isset(self::$mapper_list[$class]))
		{
			return self::$mapper_list[$class];
		}
		else
		{
			if(file_exists(self::$mapper_cache_dir.'/'.$class.'.php'))
			{
				// cached mapper
				require self::$mapper_cache_dir.'/'.$class.'.php';
			}
			else
			{
				// TODO: Create a mapper
			}
			
			$class = 'Db_Mapper_Compiled_'.$class;
			
			return self::$mapper_list[$class] = new $class();
		}
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