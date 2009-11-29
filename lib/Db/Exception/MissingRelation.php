<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that a relation is missing.
 */
class Db_Exception_MissingRelation extends Db_Exception
{
	/**
	 * The name of the relation which wasn't found.
	 * 
	 * @var string
	 */
	protected $relation_name;
	
	/**
	 * The class for which the error occurred.
	 * 
	 * @var string
	 */
	protected $class_name;
	
	function __construct($relation_name, $class_name)
	{
		parent::__construct('Missing relation: "'.$relation_name.'".');
		
		$this->relation_name = $relation_name;
		$this->class_name = $class_name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the missing relation.
	 * 
	 * @return string
	 */
	public function getRelationName()
	{
		return $this->relation_name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the class name for which the relation was requested.
	 * 
	 * @return string
	 */
	public function getClassName()
	{
		return $this->class_name;
	}
}


/* End of file MissingRelation.php */
/* Location: ./lib/Db/Exception */