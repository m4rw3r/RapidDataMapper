<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class which interacts with the database, connection specific code is provided
 * by subclasses.
 */
abstract class Rdm_Adapter
{
	/**
	 * A list of loaded Rdm_Adapter instances.
	 * 
	 * @var array(Rdm_Adapter)
	 */
	protected static $instances = array();
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the Rdm_Adapter instance, a name can be specified for fetching a
	 * specific Rdm_Adapter instance.
	 * 
	 * @throws Rdm_Adapter_ConfigurationException
	 * @param  string		Adapter configuration name
	 * @return Rdm_Adapter
	 */
	public static function getInstance($name = false)
	{
		$name = $name ? $name : Rdm_Config::getDefaultAdapterName();
		
		if( ! isset(self::$instances[$name]))
		{
			$config = Rdm_Config::getAdapterConfiguration($name);
			
			$c = $config['class'];
			
			self::$instances[$name] = new $c($name, $config);
			
			if( ! self::$instances[$name] instanceof Rdm_Adapter)
			{
				throw new Rdm_Adapter_ConfigurationException($name, 'The class "'.$config['class'].'" does not extend Rdm_Adapter.');
			}
		}
		
		return self::$instances[$name];
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns all loaded Rdm_Adapter instances.
	 * 
	 * @return array(Rdm_Adapter)
	 */
	public static function getAllInstances()
	{
		return self::$instances;
	}
	
	/**
	 * The name of this database connection configuration.
	 * 
	 * @var string
	 */
	protected $name;
	
	// ------------------------------------------------------------------------

	/**
	 * Constructor, protected to prevent other objects from instantiating adapter
	 * instances.
	 * 
	 * @param  string
	 * @param  array(string => string)
	 */
	protected function __construct($name, array $options)
	{
		$this->name = $name;
		// TODO: Code
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of this database connection's configuration.
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}


/* End of file Adapter.php */
/* Location: ./lib/Rdm */