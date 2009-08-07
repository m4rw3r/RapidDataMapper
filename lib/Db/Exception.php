<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Generic exception for the RapidDataMapper.
 */
class Db_Exception extends Exception
{
	function __construct($message, $error_no = 0, $previous = null)
	{
		parent::__construct('RapidDataMapper: ' . $message . '', $error_no, $previous);
	}
}


/* End of file MissingConfig.php */
/* Location: ./lib/Db/Exception */