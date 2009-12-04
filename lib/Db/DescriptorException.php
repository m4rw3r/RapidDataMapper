<?php
/*
 * Created by Martin Wernståhl on 2009-12-04.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Db_DescriptorException extends Db_Exception
{
	/**
	 * The class name for which the descriptor is made.
	 * 
	 * @var string
	 */
	protected $class_name;
	
	// ------------------------------------------------------------------------
	
	public function __construct($class_name, $message)
	{
		$this->class_name = $class_name;
		
		parent::__construct('Descriptor for class "'.$class_name.'": '.$message);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the class name for which the descriptor is made.
	 * 
	 * @return string
	 */
	public function getClassName()
	{
		return $this->class_name;
	}
}


/* End of file DescriptorException.php */
/* Location: ./lib/Db */