<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a database query error occurs.
 */
class Rdm_Adapter_QueryException extends Rdm_Adapter_Exception
{
	/**
	 * The SQL which resulted in the error.
	 * 
	 * @var string
	 */
	protected $err_sql = null;
	
	/**
	 * The error message from the server.
	 * 
	 * @var string
	 */
	protected $err_message = null;
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the SQL which was used.
	 * 
	 * @param  string
	 * @return void
	 */
	public function setSQL($sql)
	{
		$this->err_sql = $sql;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the SQL which resulted in the database error.
	 * 
	 * @return string
	 */
	public function getSQL()
	{
		return $this->err_sql;
	}
	
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
	 * Creates an exception telling the user that an empty query was passed to
	 * Rdm_Adapter->query().
	 * 
	 * @return Rdm_Adapter_QueryException
	 */
	public static function emptyQuery()
	{
		return new Rdm_Adapter_QueryException('Invalid query, the query is empty.', 0);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that a query failed.
	 * 
	 * @param  string
	 * @param  string
	 * @param  int
	 * @return Rdm_Adapter_QueryException
	 */
	public static function queryError($sql, $error_message, $error_code)
	{
		$e = new Rdm_Adapter_QueryException(sprintf('Query error: %d, %s, SQL: "%s"', $error_code, $error_message, $sql), $error_code);
		
		$e->setSQL($sql);
		$e->setErrorMessage($error_message);
		
		return $e;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that OFFSET is not supported when
	 * not using LIMIT.
	 * 
	 * @return Rdm_Adapter_QueryException
	 */
	public static function offsetWithoutLimit()
	{
		return new Rdm_Adapter_QueryException('OFFSET without LIMIT is not supported by the SQL standard.');
	}
}


/* End of file QueryException.php */
/* Location: ./lib/Rdm/Adapter */