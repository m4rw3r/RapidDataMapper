<?php
/*
 * Created by Martin Wernståhl on 2009-08-23.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Db_Exception_Descriptor_MissingColumnName extends Db_Exception
{
	function __construct()
	{
		parent::__construct('Missing column name');
	}
}


/* End of file MissingColumnName.php */
/* Location: ./lib/Db/Exception/Descriptor */