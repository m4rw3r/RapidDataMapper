<?php
/*
 * Created by Martin Wernståhl on 2010-04-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Collection_Exception extends Exception implements Rdm_Exception
{
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that the called method should be
	 * implemented in a child class.
	 * 
	 * @param  string
	 * @return Rdm_Collection_Exception
	 */
	public static function missingMethod($method)
	{
		return new Rdm_Collection_Exception(sprintf('This method (%s) has not been implemented. It should be implemented in child classes.', $method));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that the collection he tries to
	 * populate is a part of another collection.
	 * 
	 * @param  string  The class name of the collection which isn't the root obejct
	 * @return Rdm_Collection_Exception
	 */
	public static function notRootObject($class_name = false)
	{
		return new Rdm_Collection_Exception(sprintf('The %s object you are trying to populate is not a root Collection object, it is probably used by another collection. Ensure that you have the correct number of end() calls.', $class_name ? $class_name : 'Collection'));
	}
}


/* End of file Exception.php */
/* Location: ./lib/Rdm/Collection */