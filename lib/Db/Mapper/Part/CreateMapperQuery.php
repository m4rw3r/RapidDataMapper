<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the createMapperQuery() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_CreateMapperQuery extends Db_CodeBuilder_Method
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = 'createMapperQuery';
		
		$this->addPart('return new Db_Compiled_'.$descriptor->getClass().'MapperQuery($this);');
	}
}


/* End of file CreateMapperQuery.php */
/* Location: ./lib/Db/Mapper/Part */