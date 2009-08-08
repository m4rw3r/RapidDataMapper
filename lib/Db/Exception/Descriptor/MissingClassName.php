<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Db_Exception_Descriptor_MissingClassName extends Db_Exception
{
	function __construct()
	{
		parent::__construct('Missing class name');
	}
}


/* End of file MissingClassName.php */
/* Location: ./lib/Db/Exception/Descriptor */