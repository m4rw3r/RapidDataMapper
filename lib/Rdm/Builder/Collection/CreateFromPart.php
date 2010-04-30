<?php
/*
 * Created by Martin Wernståhl on 2010-04-08.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_CreateFromPart extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('createFromPart');
		$this->setParamList('$parent_alias, &$list');
		
		$db = $desc->getAdapter();
		
		$this->addPart('if( ! $parent_alias)
{
	// We\'re the root node, use FROM
	$list[] = \'FROM '.addcslashes($db->protectIdentifiers($db->dbprefix.$desc->getTable()), "'").' AS '.addcslashes($db->protectIdentifiers($desc->getSingular()), "'").'\';
}
else
{
	// If we have conditions for a JOINed collection, then we use INNER JOIN
	$type = isset($this->filters[0]) ? \'INNER \' : \'LEFT \';
	
	// Set the aliases for the relation filter
	$this->relation->setAliases($this->table_alias, $parent_alias);
	
	$list[] = $type.\'JOIN '.addcslashes($db->protectIdentifiers($db->dbprefix.$desc->getTable()), "'").' AS \'.$this->table_alias.\' ON \'.implode(\' \', $this->filters);
}');
		
		$this->addPart('foreach($this->with as $join_alias => $join)
{
	$join->createFromPart($this->table_alias, $list);
}');
	}
}


/* End of file CreateFromPart.php */
/* Location: ./lib/Rdm/Builder/Collection */