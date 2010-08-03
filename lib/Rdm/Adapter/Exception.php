<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Adapter_Exception extends RuntimeException implements Rdm_Exception
{
	/**
	 * Creates an exception telling the user that unserialization of the class
	 * is not allowed.
	 * 
	 * @param  string  The class name of the object which the user attempts to
	 *                 unserialize
	 * @return Rdm_Adapter_Exception
	 */
	public static function cloneNotAllowed($class)
	{
		return new Rdm_Adapter_Exception(sprintf('Cloning of %s objects is not allowed.', $class));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Creates an exception telling the user that serialization of the class
	 * is not allowed.
	 * 
	 * @param  string  The class name of the object which the user attempts to
	 *                 serialize
	 * @return Rdm_Adapter_Exception
	 */
	public static function serializeNotAllowed($class)
	{
		return new Rdm_Adapter_Exception(sprintf('Serialization of %s objects is not allowed.', $class));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that unserialization of the class
	 * is not allowed.
	 * 
	 * @param  string  The class name of the object which the user attempts to
	 *                 unserialize
	 * @return Rdm_Adapter_Exception
	 */
	public static function unserializeNotAllowed($class)
	{
		return new Rdm_Adapter_Exception(sprintf('Unserialization of %s objects are is allowed.', $class));
	}
}


/* End of file Exception.php */
/* Location: ./lib/Rdm/Adapter */