<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a database connection configuration is missing.
 */
class Db_Exception_MissingConfig extends Db_Exception
{
	/**
	 * The name of the configuration which wasn't found.
	 * 
	 * @var string
	 */
	public $config_name;
	
	function __construct($config_name)
	{
		parent::__construct('Missing configuration: "'.$config_name.'".');
		
		$this->config_name = $config_name;
	}
}


/* End of file MissingConfig.php */
/* Location: ./lib/Db/Exception */