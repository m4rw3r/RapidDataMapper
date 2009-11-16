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
			
			// add column mappings
			$php_names = array();
			$sql_names = array();
			foreach($rel->getRelatedDescriptor()->getColumns() as $col)
			{
				$p = $col->getLocalColumn('$alias_of_linked-'.$rel->getName());
				$s = $col->getSourceColumn('$alias_of_linked-'.$rel->getName());
				
				// smaller optimization, makes for fewer replaces
				if(strtolower($p) != strtolower($s))
				{
					$php_names[] = '"'.addcslashes($p, '"').'"';
					$sql_names[] = '"'.addcslashes($s, '"').'"';
				}
			}
			
			if( ! empty($php_names))
			{
				$this->addPart("\t\t// Add column translations\n\t\t\$query->php_columns = array_merge(\$query->php_columns, array(".implode(', ', $php_names)."));
		\$query->sql_columns = array_merge(\$query->sql_columns, array(".implode(', ', $sql_names)."));");
			}
			
			$this->addPart("\t\tbreak;");
		}
		
		$this->addPart('}');
	}
}


/* End of file JoinRelated.php */
/* Location: ./lib/Db/Mapper/Part */