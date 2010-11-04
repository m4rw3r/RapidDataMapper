<?php
/*
 * Created by Martin Wernståhl on 2010-11-24.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Default annotation factory.
 * 
 * TODO: Make it respect the isInheritable(), isMethodAnnotation() etc.
 */
class Rdm_Util_Annotation_Factory implements Rdm_Util_Annotation_FactoryInterface
{
	protected $annotation_class_prefix = '';
	
	protected $aliases = array();
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __construct($annotation_class_prefix = '')
	{
		$this->annotation_class_prefix = $annotation_class_prefix;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function createObject($annotation_name, $params)
	{
		$class = $this->alias2class($annotation_name);
		
		if(empty($params))
		{
			return new $class;
		}
		else
		{
			return new $class($params);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function setAlias($class, $alias)
	{
		$this->aliases[strtolower($class)] = $alias;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function alias2class($alias)
	{
		return empty($this->aliases[$alias]) ? $this->annotation_class_prefix.$alias : $this->annotation_class_prefix.$this->aliases[$alias];
	}
}


/* End of file Factory.php */
/* Location: ./lib/Rdm/Util/Annotation */