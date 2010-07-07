<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a database connection error occurs.
 */
class Rdm_Adapter_ConnectionException extends RuntimeException implements Rdm_Exception
{
	/**
	 * The error message from the server.
	 * 
	 * @var string
	 */
	protected $err_message = null;
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Sets the error message from the server.
	 * 
	 * @param  string
	 * @return void
	 */
	public function setErrorMessage($msg)
	{
		$this->err_message = $msg;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the database error message.
	 * 
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->err_message;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that RDM couldn't connect to the database.
	 * 
	 * @param  string
	 * @param  string
	 * @param  int
	 * @return Rdm_Adapter_ConnectionException
	 */
	public static function couldNotConnect($hostname, $error_message, $error_no)
	{
		$e = new Rdm_Adapter_ConnectionException(sprintf('Could not connect to host "%s": %d, %s', $hostname, $error_no, $error_message), $error_no);
		
		$e->setErrorMessage($error_message);
		
		return $e;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that RDM couldn't select the
	 * requested database.
	 * 
	 * @param  string
	 * @param  string
	 * @param  int
	 * @return Rdm_Adapter_ConnectionException
	 */
	public static function couldNotSelect($database, $error_message, $error_no)
	{
		$e = new Rdm_Adapter_ConnectionException(sprintf('Could not select requested database for connection "%s": %d, %s', $database, $error_no, $error_message), $error_no);
		
		$e->setErrorMessage($error_message);
		
		return $e;
	}
}


/* End of file ConnectionException.php */
/* Location: ./lib/Rdm/Adapter */