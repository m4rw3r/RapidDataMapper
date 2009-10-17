<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the Objectify() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_Objectify extends Db_Mapper_Code_Method
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = 'objectify';
		$this->param_list = '&$res, $row, $alias, &$mappers, $alias_paths';
		
		$this->addPart('if('.$descriptor->getNotContainsObjectCode('$row', '$alias').')
{
	return null;
}');
		
		$this->addPart('$uid = '.$descriptor->getUidCode('$row', '$alias').';');
		
		$this->addPart(new Db_Mapper_Part_Objectify_NewObj($descriptor));
		
		$this->addPart(new Db_Mapper_Part_Objectify_LoadRelated($descriptor));
	}
}


/* End of file Objectify.php */
/* Location: ./lib/Db/Mapper/Part */