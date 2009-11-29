<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a query error occurs.
 */
class Db_Exception_QueryError extends Db_Exception
{
	/**
	 * The error message.
	 * 
	 * @var string
	 */
	protected $error_message;
	
	function __construct($error_message)
	{
		parent::__construct('Query Error: '.$error_message.'.');
		
		$this->error_message = $error_message;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the query error message.
	 * 
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->error_message;
	}
}


/* End of file QueryError.php */
/* Location: ./lib/Db/Exception */