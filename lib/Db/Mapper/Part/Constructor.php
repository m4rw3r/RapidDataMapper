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
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = '__construct';
		
		$conn_name = $descriptor->getConnectionName();
		
		if( ! empty($conn_name))
		{
			$conn_name = "'".addcslashes($conn_name, "'")."'";
		}
		
		$this->addPart('parent::__construct(Db::getConnection('.$conn_name.'));');
	}
}


/* End of file Constructor.php */
/* Location: ./lib/Db/Mapper/Part */