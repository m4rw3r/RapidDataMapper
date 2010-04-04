<?php
/*
 * Created by Martin Wernståhl on 2010-04-03.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Configuration container for RapidDataMapper.
 */
class Rdm_Config
{
	/**
	 * A list of adapter configurations.
	 * 
	 * @var array(string => array)
	 */
	protected static $adapter_configs = array();
	
	/**
	 * The name for the default adapter configuration.
	 * 
	 * @var string
	 */
	protected static $adapter_default_name = 'default';
	
	/**
	 * Value containing setting for mapper caching.
	 * 
	 * @var boolean
	 */
	protected static $mappers_cache = false;
	
	/**
	 * The directory in which the cached mappers will be stored.
	 * 
	 * @var string
	 */
	protected static $mappers_cache_dir = '.';
	
	/**
	 * A list of descriptors describing mappings of entities to the database.
	 * 
	 * @var array(string => Rdm_Descriptor)
	 */
	protected static $descriptors = array();
	
	/**
	 * A cascade of descriptor loaders.
	 * 
	 * @var array(callback)
	 */
	protected static $descriptor_loaders = array();
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the adapter configuration for a certain database connection.
	 * 
	 * @throws Rdm_Adapter_ConfigurationException
	 * @param  string|array
	 * @param  array
	 * @return void
	 */
	public static function setAdapterConfiguration($name, $configuration = false)
	{
		if(is_array($name))
		{
			self::$adapter_configs = array_merge(self::$adapter_configs, $name);
		}
		elseif(empty($configuration))
		{
			throw new Rdm_Adapter_ConfigurationException($name, 'Invalid configuration.');
		}
		else
		{
			self::$adapter_configs[$name] = $configuration;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the adapter configuration for the supplied name.
	 * 
	 * @throws Rdm_Adapter_ConfigurationException
	 * @param  string
	 * @return array(string => string)
	 */
	public static function getAdapterConfiguration($name)
	{
		if(empty(self::$adapter_configs[$name]))
		{
			throw new Rdm_Adapter_ConfigurationException($name, 'Missing configuration.');
		}
		elseif(empty(self::$adapter_configs[$name]['class']))
		{
			throw new Rdm_Adapter_ConfigurationException($name, 'Missing "class" key in configuration.');
		}
		else
		{
			return self::$adapter_configs[$name];
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the default adapter configuration, default: "default".
	 * 
	 * @param  string
	 * @return void
	 */
	public static function setDefaultAdapterName($name)
	{
		self::$adapter_default_name = $name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the default Rdm_Adapter instance.
	 * 
	 * @return string
	 */
	public static function getDefaultAdapterName()
	{
		return self::$adapter_default_name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Dictates if the mappers should be cached or not, true = cached.
	 * 
	 * @param  boolean
	 * @return void
	 */
	public static function setCacheMappers($value = true)
	{
		self::$mappers_cache = $value;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the value determining if 
	 * 
	 * @return boolean
	 */
	public static function getCacheMappers()
	{
		return self::$mappers_cache;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the directory to store the cached mappers.
	 * 
	 * @param  string
	 * @return void
	 */
	public static function setMapperCacheDir($path)
	{
		self::$mappers_cache_dir = $path;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the directory in which the mappers will be cached.
	 * 
	 * @return string
	 */
	public static function getMapperCacheDir()
	{
		return self::$mappers_cache_dir;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a descriptor instance to the configuration.
	 * 
	 * @throws RuntimeException
	 * @param  Rdm_Descriptor
	 * @return void
	 */
	public static function addDescriptor(Rdm_Descriptor $descriptor)
	{
		if(isset(self::$descriptors[strtolower($descriptor->getClass())]))
		{
			throw new RuntimeException('RapidDataMapper: The descriptor for the class "'.$descriptor->getClass().'" has already been loaded.');
		}
		
		self::$descriptors[strtolower($descriptor->getClass())] = $descriptor;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a callback which will be called for missing descriptors.
	 *
	 * Method signature of the callable:
	 * <code>
	 * Rdm_Descriptor $callback(string $class_name);
	 * </code>
	 * 
	 * If anything but a Rdm_Descriptor (or a child class) is returned, it will
	 * be considered a failure, and RapidDataMapper will search elsewhere for
	 * a descriptor.
	 * 
	 * Multiple descriptor loaders are supported, and they are called in the
	 * order that they have been registered in. It goes through all of them
	 * until one of them returns a descriptor.
	 * 
	 * @throws InvalidArgumentException
	 * @param  callback
	 * @return void
	 */
	public static function addDescriptorLoader($callback)
	{
		if( ! is_callable($callable, true))
		{
			throw new InvalidArgumentException('Faulty syntax in supplied callable.');
		}
		
		self::$descriptor_loaders[] = $callable;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns an array with the registered descriptor loaders.
	 * 
	 * @return array(callback)
	 */
	public static function getDescriptorLoaders()
	{
		return self::$descriptor_loaders;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the path to the directory in which RapidDataMapper should look for
	 * descriptors if it cannot find them anywhere else.
	 * 
	 * @param  string
	 * @return void
	 */
	public static function setDescriptorDir($path)
	{
		self::$descriptor_dir = $path;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the descriptor for a certain class.
	 * 
	 * - First checks if there already is a loaded descriptor
	 *   (or manually loaded, via addDescriptor()).
	 * - After that it checks if there is a registered descriptor
	 *   loader, which would return a descriptor instance describing the class.
	 * - Then it checks if ClassNameDescriptor.php exists
	 *   (it uses the autoloader(s), so Record_UserDescriptor will be placed in Record/UserDescriptor.php).
	 * - Finally it tries the descriptor directory for any files with the name
	 *   ClassName.php, which will contain a ClassNameDescriptor class (no autoloader).
	 * 
	 * @param  string
	 * @throws Rdm_Descriptor_MissingException
	 * @return Rdm_Descriptor
	 */
	public static function getDescriptor($class)
	{
		// Strtolower so we're sure that we don't load it twice
		$c = strtolower($class);
		
		if(isset(self::$descriptors[$c]))
		{
			return self::$descriptors[$c];
		}
		
		// check if we can call a mapper descriptor loader (also autoload it, and see if it can be called)
		foreach(self::$descriptor_loaders as $loader)
		{
			// check that we get a Rdm_Descriptor object
			if(is_callable($loader) && ($d = call_user_func($loader, $class)) instanceof Rdm_Descriptor)
			{
				// We've got a working instance, save and return
				return self::$descriptors[$c] = $d;
			}
		}
		
		// default class name
		$klass = $class.'Descriptor';
		
		// do we have a descriptor class? (it also tries to autoload it with class_exists())
		if( ! class_exists($klass))
		{
			// do we have a certain descriptor file?
			if(file_exists(self::$descriptor_dir.'/'.$class.'.php'))
			{
				require self::$descriptor_dir.'/'.$class.'.php';
			}
			
			// check if any class was loaded, do not use autoload this time
			if( ! class_exists($klass, false))
			{
				throw new Rdm_Descriptor_MissingException($class, 'Descriptor is missing.');
			}
		}
		
		// Create instance
		return self::$descriptors[$c] = new $klass();
	}
}


/* End of file Config.php */
/* Location: ./lib/Rdm */