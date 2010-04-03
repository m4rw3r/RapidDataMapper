<?php
/*
 * Created by Martin Wernståhl on 2009-08-23.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a value is missing.
 */
class Rdm_Descriptor_MissingValueException extends Rdm_Descriptor_Exception
{
	/**
	 * The name of the value which wasn't found.
	 * 
	 * @var string
	 */
	protected $value_name;
	
	function __construct($value_name, $class_name)
	{
		parent::__construct($class_name, 'Missing value: "'.$value_name.'"');
		
		$this->value_name = $value_name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the missing value.
	 * 
	 * @return string
	 */
	public function getValueName()
	{
		return $this->value_name;
	}
}


/* End of file MissingValueException.php */
/* Location: ./lib/Rdm/Descriptor */