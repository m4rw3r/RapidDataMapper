<?php
/*
 * Created by Martin Wernståhl on 2009-08-24.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a connection configuration is malformed.
 */
class Db_Exception_InvalidConfiguration extends Db_Exception
{
	/**
	 * The name of the malformed configuration, if present.
	 * 
	 * @var string
	 */
	public $name;
	
	function __construct($name)
	{
		parent::__construct('Malformed configuration: "'.$name.'".');
		
		$this->name = $name;
	}
}


/* End of file InvalidConfiguration.php */
/* Location: ./lib/Db/Exception */