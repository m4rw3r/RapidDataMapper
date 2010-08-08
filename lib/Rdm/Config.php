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
	 * The database adapter to use for this configuration.
	 * 
	 * @var Rdm_Adapter
	 */
	protected $adapter = null;
	
	/**
	 * Value containing setting for mapper caching.
	 * 
	 * @var boolean
	 */
	protected $mappers_cache = false;
	
	/**
	 * The directory in which the cached mappers will be stored.
	 * 
	 * @var string
	 */
	protected $mappers_cache_dir = '.';
	
	/**
	 * A list of descriptors describing mappings of entities to the database.
	 * 
	 * @var array(string => Rdm_Descriptor)
	 */
	protected $descriptors = array();
	
	/**
	 * A cascade of descriptor loaders.
	 * 
	 * @var array(callback)
	 */
	protected $descriptor_loaders = array();
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the database adapter instance to be used by this configuration.
	 * 
	 * @param  Rdm_Adapter
	 * @return void
	 */
	public function setAdapter(Rdm_Adapter $db)
	{
		$this->adapter = $db;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the db adapter instance used by this configuration.
	 * 
	 * @return Rdm_Adapter
	 */
	public function getAdapter()
	{
		// TODO: Check if we have an adapter?
		return $this->adapter;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Dictates if the mappers should be cached or not, true = cached.
	 * 
	 * @param  boolean
	 * @return void
	 */
	public function setCacheMappers($value = true)
	{
		$this->mappers_cache = $value;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the value determining if mappers should be cached or not.
	 * 
	 * @return boolean
	 */
	public function getCacheMappers()
	{
		return $this->mappers_cache;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the directory to store the cached mappers.
	 * 
	 * @param  string
	 * @return void
	 */
	public function setMapperCacheDir($path)
	{
		$this->mappers_cache_dir = $path;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the directory in which the mappers will be cached.
	 * 
	 * @return string
	 */
	public function getMapperCacheDir()
	{
		return $this->mappers_cache_dir;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a descriptor instance to the configuration.
	 * 
	 * @throws RuntimeException
	 * @param  Rdm_Descriptor
	 * @return void
	 */
	public function addDescriptor(Rdm_Descriptor $descriptor)
	{
		if(isset($this->descriptors[strtolower($descriptor->getClass())]))
		{
			throw new RuntimeException('RapidDataMapper: The descriptor for the class "'.$descriptor->getClass().'" has already been loaded.');
		}
		
		$this->descriptors[strtolower($descriptor->getClass())] = $descriptor;
		
		$descriptor->setConfig($this);
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
	public function addDescriptorLoader($callback)
	{
		if( ! is_callable($callback, true))
		{
			throw new InvalidArgumentException('Faulty syntax in supplied callback.');
		}
		
		$this->descriptor_loaders[] = $callback;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns an array with the registered descriptor loaders.
	 * 
	 * @return array(callback)
	 */
	public function getDescriptorLoaders()
	{
		return $this->descriptor_loaders;
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
	 * 
	 * @param  string
	 * @throws Rdm_Descriptor_MissingException
	 * @return Rdm_Descriptor
	 */
	public function getDescriptor($class)
	{
		// Strtolower so we're sure that we don't load it twice
		$c = strtolower($class);
		
		if(isset($this->descriptors[$c]))
		{
			return $this->descriptors[$c];
		}
		
		// check if we can call a mapper descriptor loader (also autoload it, and see if it can be called)
		foreach($this->descriptor_loaders as $loader)
		{
			// check that we get a Rdm_Descriptor object
			if(is_callable($loader) && ($d = call_user_func($loader, $class)) instanceof Rdm_Descriptor)
			{
				// We've got a working instance, save and return
				$this->descriptors[$c] = $d;
				
				$d->setConfig($this);
				
				return $d;
			}
		}
		
		// default class name
		$klass = $class.'Descriptor';
		
		// do we have a descriptor class? (it also tries to autoload it with class_exists())
		if( ! class_exists($klass))
		{
			throw new Rdm_Descriptor_MissingException($class);
		}
		
		// Create instance
		$this->descriptors[$c] = $desc = new $klass();
		
		$desc->setConfig($this);
		
		return $desc;
	}
}


/* End of file Config.php */
/* Location: ./lib/Rdm */