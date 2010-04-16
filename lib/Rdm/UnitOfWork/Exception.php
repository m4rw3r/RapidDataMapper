<?php
/*
 * Created by Martin Wernståhl on 2010-04-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_UnitOfWork_Exception extends Exception implements Rdm_Exception
{
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that the object already is persisted.
	 * 
	 * @param  object
	 * @return Rdm_UnitOfWork_Exception
	 */
	public static function alreadyPersisted($object)
	{
		return new Rdm_UnitOfWork_Exception(sprintf('The object of type %s is already being persisted.', get_class($object)));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates an exception telling the user that the object already has been
	 * deleted.
	 * 
	 * @param  object
	 * @return Rdm_UnitOfWork_Exception
	 */
	public function alreadyDeleted($object)
	{
		return new Rdm_UnitOfWork_Exception(sprintf('The object of type %s has already been deleted.', get_class($object)));
	}
}


/* End of file Exception.php */
/* Location: ./lib/Rdm/UnitOfWork */