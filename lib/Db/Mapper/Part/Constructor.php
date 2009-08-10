<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the __constructor() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_Constructor extends Db_Mapper_Code_Method
{
	protected $descriptor;
	
	function __construct(Db_Descriptor $desc)
	{
		$this->name = '__construct';
		
		$this->descriptor = $desc;
		
		$this->addContents();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds the default contents of this method.
	 * 
	 * @return void
	 */
	public function addContents()
	{
		$conn_name = $this->descriptor->getDatabaseConnectionName();
		
		if( ! empty($conn_name))
		{
			$conn_name = "'".addcslashes($conn_name, "'")."'";
		}
		
		$this->addPart('$this->connection = Db::getConnection('.$conn_name.');');
	}
}


/* End of file Constructor.php */
/* Location: ./lib/Db/Mapper/Part */