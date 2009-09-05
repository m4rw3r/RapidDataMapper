<?php
/*
 * Created by Martin Wernståhl on 2009-09-05.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a query is incomplete when it is built.
 */
class Db_Exception_QueryIncomplete extends Db_Exception
{
	/**
	 * The error message.
	 * 
	 * @var string
	 */
	public $error_message;
	
	function __construct($error_message)
	{
		parent::__construct('Query Incomplete: '.$error_message.'.');
		
		$this->error_message = $error_message;
	}
}


/* End of file QueryIncomplete.php */
/* Location: ./lib/Db/Exception */