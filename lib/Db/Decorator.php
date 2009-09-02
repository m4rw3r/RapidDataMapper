<?php
/*
 * Created by Martin Wernståhl on 2009-09-02.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A decorator, to decorate an arbitrary object with new methods.
 */
abstract class Db_Decorator
{
	/**
	 * The object which is decorated.
	 * 
	 * @var object
	 */
	protected $decorated_object = null;
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the object to decorate.
	 * 
	 * @param  object
	 * @return void
	 */
	final public function setDecoratedObject($object)
	{
		$this->decorated_object = $object;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the object which is decorated.
	 * 
	 * @throws UnexpectedValueException
	 * @return object
	 */
	final public function getDecoratedObject()
	{
		if( ! is_object($this->decorated_object))
		{
			throw new UnexpectedValueException();
		}
		
		return $this->decorated_object;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Forwards uncatched method calls to the decorated object.
	 * 
	 * @param  string
	 * @param  array
	 * @return mixed
	 */
	public function __call($method, $params)
	{
		if(method_exists($this->decorated_object, $method))
		{
			return call_user_func_array(array($this->decorated_object, $method), $params);
		}
		
		throw new BadMethodCallException($method);
	}
}


/* End of file Decorator.php */
/* Location: ./lib/Db.php */