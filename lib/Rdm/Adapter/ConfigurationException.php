<?php
/*
 * Created by Martin Wernståhl on 2009-08-24.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a connection configuration is malformed.
 */
class Rdm_Adapter_ConfigurationException extends DomainException implements Rdm_Exception
{
	/**
	 * Creates an exception which tells the user that the requested adapter does
	 * not extend the required base class.
	 * 
	 * @param  string
	 * @param  string
	 * @return Rdm_Adapter_ConfigurationException
	 */
	public static function notUsingBaseClass($config_name, $adapter_class)
	{
		return new Rdm_Adapter_ConfigurationException(sprintf('The configuration "%s" tries to use an adapter class (%s) which does not extend the base class Rdm_Adapter.', $config_name, $adapter_class));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception which tells the user that the options supplied to the
	 * adapter is missing required keys.
	 * 
	 * @param  string
	 * @param  array(string)
	 * @return Rdm_Adapter_ConfigurationException
	 */
	public static function missingOptions($config_name, array $req_keys)
	{
		return new Rdm_Adapter_ConfigurationException(sprintf('The configuration "%s" is missing required keys: "%s"', $config_name, implode('", "', $req_keys)));
	}
}


/* End of file ConfigurationException.php */
/* Location: ./lib/Rdm/Adapter */