<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that an object descriptor is missing.
 */
class Db_Exception_MissingDescriptor extends Db_Exception
{
	/**
	 * The name of the class for which a descriptor wasn't found.
	 * 
	 * @var string
	 */
	public $class_name;
	
	function __construct($class_name)
	{
		parent::__construct('Missing descriptor for the class: "'.$class_name.'".');
		
		$this->class_name = $class_name;
	}
}


/* End of file MissingDescriptor.php */
/* Location: ./lib/Db/Exception */