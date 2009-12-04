<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a database connection error occurs.
 */
class Db_ConnectionException extends Db_Exception
{
	/**
	 * The error message.
	 * 
	 * @var string
	 */
	protected $error_message;
	
	function __construct($error_message)
	{
		parent::__construct('Connection Error: "'.$error_message.'".');
		
		$this->error_message = $error_message;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the error message.
	 * 
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->error_message;
	}
}


/* End of file ConnectionException.php */
/* Location: ./lib/Db */