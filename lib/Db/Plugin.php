<?php
/*
 * Created by Martin Wernståhl on 2009-09-02.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A base class for plugins.
 */
abstract class Db_Plugin
{
	/**
	 * The descriptor which this plugin is associated with.
	 * 
	 * @var Db_Descriptor
	 */
	protected $descriptor;
	
	// ------------------------------------------------------------------------

	/**
	 * Sets which descriptor to use.
	 * 
	 * @param  Db_Descriptor
	 * @return void
	 */
	final public function setDescriptor(Db_Descriptor $desc)
	{
		$this->descriptor = $desc;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Initializes the plugin, the descriptor property is set.
	 * 
	 * @return void
	 */
	public function init(){ }
	
	// ------------------------------------------------------------------------

	/**
	 * Provides a chance for the plugin to edit the builder before it renders the class.
	 * 
	 * @param  Db_Mapper_Builder
	 * @return void
	 */
	public function editBuilder($builder){ }
	
	// ------------------------------------------------------------------------

	/**
	 * Removes a plugin from the descriptor it is assigned to.
	 * 
	 * @return 
	 */
	public function remove(){ }
	
	// ------------------------------------------------------------------------

	/**
	 * Determines if a particular decorator has been wrapped around the $object.
	 * 
	 * @param  object
	 * @param  string		Name of a subclass to Db_Decorator
	 * @return bool
	 */
	public static function hasDecorator($object, $decorator_class)
	{
		if( ! is_object($object))
		{
			throw new InvalidArgumentException(gettype($object));
		}
		
		if($object instanceof $decorator_class)
		{
			return true;
		}
		elseif($object instanceof Db_Decorator)
		{
			return self::hasDecorator($object->getDecoratedObject(), $decorator_class);
		}
		else
		{
			return false;
		}
	}
}


/* End of file Plugin.php */
/* Location: ./lib/Db */