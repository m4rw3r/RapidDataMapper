<?php
/*
 * Created by Martin Wernståhl on 2010-04-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Collection_Exception extends RuntimeException implements Rdm_Exception
{
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
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that the collection contains filters
	 * which are dynamic and hence they cannot set the values on the supplied object.
	 * 
	 * @return Rdm_Collection_Exception
	 */
	public static function filterCannotModify()
	{
		return new Rdm_Collection_Exception('Filters does not only contain fixed values, cannot apply changes to the supplied object. (This can be caused by filters like id < 34 or OR conditionals.)');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that an object of type $class_name
	 * was expected.
	 * 
	 * @param  string
	 * @return Rdm_Collection_Exception
	 */
	public static function expectingObjectOfClass($class_name)
	{
		return new Rdm_Collection_Exception('Object of type '.$class_name.' was expected.');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that the object is locked and its
	 * filters couldn't be modified.
	 * 
	 * @return Rdm_Collection_Exception
	 */
	 public static function objectLocked()
	{
		return new Rdm_Collection_Exception('Object is locked because queries has already been issued to the database'.
			'(Examples can be for additions to the collection, fetching of its contents or removals).');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that the object already has been
	 * populated with data.
	 * 
	 * @return Rdm_Collection_Exception
	 */
	public static function objectAlreadyPopulated()
	{
		return new Rdm_Collection_Exception('The collection object has already been populated.');
	}
}


/* End of file Exception.php */
/* Location: ./lib/Rdm/Collection */