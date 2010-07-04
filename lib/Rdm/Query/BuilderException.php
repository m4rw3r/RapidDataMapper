<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that an error occurs during query building.
 */
class Rdm_Query_BuilderException extends RuntimeException implements Rdm_Exception
{
	/**
	 * Creates an exception telling the user that data is missing for a certain
	 * type of query.
	 * 
	 * @param  string
	 * @return Rdm_Query_BuilderException
	 */
	public static function missingData($query_type)
	{
		return new Rdm_Query_BuilderException(sprintf('Missing data for %s query.', $query_type));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that a FROM part is missing from
	 * a certain type of query.
	 * 
	 * @return Rdm_Query_BuilderException
	 */
	public static function missingFrom($query_type)
	{
		return new Rdm_Query_BuilderException(sprintf('Missing FROM part for %s query.', $query_type));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that the data which he/she wishes
	 * to assign to the column does not match
	 * 
	 * @return Rdm_Query_BuilderException
	 */
	public static function missingInsertColumns()
	{
		return new Rdm_Query_BuilderException('Missing a list of columns for INSERT query.');
	}
}


/* End of file BuilderException.php */
/* Location: ./lib/Rdm/Query */