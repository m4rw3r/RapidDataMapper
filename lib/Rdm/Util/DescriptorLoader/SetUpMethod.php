<?php
/*
 * Created by Martin Wernståhl on 2010-05-08.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Descriptor loader which loads descriptor by passing an empty Rdm_Descriptor to
 * a static setUp method located on the entity which will populate the descriptor.
 * 
 * Usage:
 * <code>
 * // Init loader:
 * $config->addDescriptorLoader(array(new Rdm_Util_DescriptorLoader_SetUpMethod(), 'load'));
 * 
 * // Entity:
 * class User
 * {
 *     public static function setUp(Rdm_Descriptor $desc)
 *     {
 *         $desc->setClass(__CLASS__);
 *         $desc->add($desc->newPrimaryKey('id'));
 *         // ...
 *     }
 *     
 *     public $id;
 *     // ...
 * }
 * </code>
 */
class Rdm_Util_DescriptorLoader_SetUpMethod
{
	/**
	 * The method name of the entity method to call to populate the descriptor.
	 * 
	 * @var string
	 */
	protected $method_name = 'setUp';
	
	/**
	 * Creates a setUp method descriptor loader which will call the
	 * $method_name with an empty descriptor to have it populate it.
	 * 
	 * @param  string
	 */
	function __construct($method_name = 'setUp')
	{
		$this->method_name = $method_name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Loads a descriptor for the supplied class.
	 * 
	 * @param  string
	 * @return Rdm_Descriptor|false
	 */
	public function load($class)
	{
		// Do we have a method which we can call?
		if(is_callable($class.'::'.$this->method_name))
		{
			// Yup, create the descriptor
			$desc = $this->getNewDescriptorInstance();
			
			call_user_func($class.'::'.$this->method_name, $desc);
			
			return $desc;
		}
		
		return false;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Template method which creates instances of the descriptor used to describe
	 * the entity mappings, these descriptors will be passed to the setUp method.
	 * 
	 * @return Rdm_Descriptor
	 */
	public function getNewDescriptorInstance()
	{
		return new Rdm_Descriptor();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Magic method for PHP 5.3 so it is possible to pass the object directly
	 * to the Rdm_Config->addDescriptorLoader() method.
	 * 
	 * @param  string
	 * @return Rdm_Descriptor|false
	 */
	public function __invoke($class)
	{
		return $this->load($class);
	}
}


/* End of file SetUpMethod.php */
/* Location: ./lib/Rdm/Util/DescriptorLoader */