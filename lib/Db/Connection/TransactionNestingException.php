<?php
/*
 * Created by Martin Wernståhl on 2010-01-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a user tries to nest transactions
 */
class Db_Connection_TransactionNestingException extends Db_Exception
{
	function __construct()
	{
		parent::__construct('Transaction nesting is not allowed.');
	}
}


/* End of file TransactionNestingException.php */
/* Location: ./lib/Db/Connection */