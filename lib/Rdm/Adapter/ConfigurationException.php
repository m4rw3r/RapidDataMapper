<?php
/*
 * Created by Martin Wernståhl on 2009-08-24.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a connection configuration is malformed.
 */
class Rdm_Adapter_ConfigurationException extends Exception implements Rdm_Exception
{
	/**
	 * The name of the malformed configuration.
	 * 
	 * @var string
	 */
	protected $config_name;
	
	function __construct($config_name, $message)
	{
		parent::__construct('Rdm_Adapter configuration with name "'.$config_name.'": '.$message);
		
		$this->config_name = $config_name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the configuration that is malformed.
	 * 
	 * @return string
	 */
	public function getConfigurationName()
	{
		return $this->config_name;
	}
}


/* End of file ConfigurationException.php */
/* Location: ./lib/Rdm/Adapter */