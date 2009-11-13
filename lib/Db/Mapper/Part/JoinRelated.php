<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the joinRelated() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_JoinRelated extends Db_CodeBuilder_Method
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = 'joinRelated';
		$this->param_list = '$query, $relation_name, $alias_of_linked';
		
		$this->addPart('switch($relation_name)
{');
		
		foreach($descriptor->getRelations() as $rel)
		{
			$this->addPart("\tcase '".$rel->getName()."':");
			$this->addPart("\t\t".self::indentCode(self::indentCode($rel->getJoinRelatedCode('$query', '$alias_of_linked'))));
			$this->addPart("\t\tbreak;");
		}
		
		$this->addPart('}');
	}
}


/* End of file JoinRelated.php */
/* Location: ./lib/Db/Mapper/Part */