<?php
/*
 * Created by Martin Wernståhl on 2009-08-24.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a connection configuration is malformed.
 */
class Db_Connection_MissingBindParameterException extends Db_Exception
{
	/**
	 * The name of the missing parameter.
	 * 
	 * @var string
	 */
	protected $name;
	
	function __construct($config_name, $message)
	{
		parent::__construct('Missing Bind Parameter "'.$name.'"');
		
		$this->name = $name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the parameter that is missing.
	 * 
	 * @return string
	 */
	public function getParameterName()
	{
		return $this->name;
	}
}


/* End of file MissingBindParameterException.php */
/* Location: ./lib/Db/Connection */