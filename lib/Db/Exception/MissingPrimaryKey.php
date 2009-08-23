<?php
/*
 * Created by Martin Wernståhl on 2009-08-23.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that an object's primary key definition is missing.
 */
class Db_Exception_MissingPrimaryKey extends Db_Exception
{
	/**
	 * The name of the class for which a primary wasn't found.
	 * 
	 * @var string
	 */
	public $class_name;
	
	function __construct($class_name)
	{
		parent::__construct('Missing primary key for the class: "'.$class_name.'".');
		
		$this->class_name = $class_name;
	}
}


/* End of file MissingPrimaryKey.php */
/* Location: ./lib/Db/Exception */