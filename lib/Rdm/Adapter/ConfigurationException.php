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
	 * Creates an exception which tells the user that the options supplied to the
	 * adapter is missing required keys.
	 * 
	 * @param  array(string)
	 * @return Rdm_Adapter_ConfigurationException
	 */
	public static function missingOptions(array $req_keys)
	{
		return new Rdm_Adapter_ConfigurationException(sprintf('The supplied adapter configuration is missing required keys: "%s"', implode('", "', $req_keys)));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that the redirect_write option requires
	 * an Rdm_Adapter instance as parameter.
	 * 
	 * @param  mixed
	 * @return Rdm_Adapter_ConfigurationException
	 */
	public static function redirectWriteFaultyParameter($faulty_data)
	{
		return new Rdm_Adapter_ConfigurationException(sprintf('The redirect_write key of the configuration is of the wrong type (%s) instead of an Rdm_Adapter instance.', is_object($faulty_data) ? get_class($faulty_data) : gettype($faulty_data)));
	}
}


/* End of file ConfigurationException.php */
/* Location: ./lib/Rdm/Adapter */