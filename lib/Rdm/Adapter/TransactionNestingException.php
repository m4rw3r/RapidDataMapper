<?php
/*
 * Created by Martin Wernståhl on 2010-01-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a user tries to nest transactions
 */
class Rdm_Adapter_TransactionNestingException extends Exception implements Rdm_Exception
{
	function __construct()
	{
		parent::__construct('Transaction nesting is not allowed.');
	}
}


/* End of file TransactionNestingException.php */
/* Location: ./lib/Rdm/Adapter */