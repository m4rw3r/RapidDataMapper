<?php
/*
 * Created by Martin Wernståhl on 2010-11-24.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Descriptor loader which generates a descriptor from annotations added on the
 * class/properties.
 * 
 * Syntax:
 * <code>
 * class User
 * {
 *     #[PrimaryKey]
 *     public $id;
 * 
 *     #[Column]
 *     public $name;
 * 
 *     #[Relation]
 *     /** 
 *      * This is a property relating to the Post class with a has many type.
 *      *\/
 *     public $posts = array(); 
 * }
 * </code>
 */
class Rdm_Util_DescriptorLoader_Annotation
{
	protected $factory;
	
	protected $reader;
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __construct()
	{
		$this->factory = new Rdm_Util_DescriptorLoader_Annotation_Factory();
		$this->reader = new Rdm_Util_Annotation_Reader($this->factory);
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
		try
		{
			$r = new ReflectionClass($class);
			$d = new Rdm_Descriptor();
			
			$this->factory->setDescriptor($d);
			$this->factory->setClass($r);
			
			$arr = $this->reader->readAnnotations($r);
			$has_annotations = ! empty($arr);
			
			// Scan properties for annotations
			foreach($r->getProperties() as $p)
			{
				$this->factory->setCurrent($p);
				$arr = $this->reader->readAnnotations($p);
				$has_annotations = ( ! empty($arr)) OR $has_annotations;
			}
			
			// TODO: Scan methods for annotations
			
			if($has_annotations)
			{
				return $d;
			}
		}
		catch(Exception $e)
		{
			// TODO: Proper exception
			throw $e;
		}
		
		return false;
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


/* End of file Annotation.php */
/* Location: ./lib/Rdm/Util/DescriptorLoader */