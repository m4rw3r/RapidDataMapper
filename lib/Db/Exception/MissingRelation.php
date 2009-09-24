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
	public $relation_name;
	
	function __construct($relation_name)
	{
		parent::__construct('Missing relation: "'.$relation_name.'".');
		
		$this->relation_name = $relation_name;
	}
}


/* End of file MissingRelation.php */
/* Location: ./lib/Db/Exception */