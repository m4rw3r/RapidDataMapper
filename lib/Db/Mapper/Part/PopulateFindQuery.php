<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the Objectify() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_PopulateFindQuery extends Db_Mapper_Code_Method
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = 'populateFindQuery';
		
		$db = $descriptor->getConnection();
		
		$this->addPart('$q = new Db_Query_MapperSelect($this, \''.$descriptor->getSingular().'\');');
		
		$columns = $descriptor->getSelectCode($descriptor->getSingular(), $descriptor->getSingular());
		
		$this->addPart('$q->columns[] = \''.addcslashes($columns, "'").'\';
$q->from[] = \''.addcslashes($db->protectIdentifiers($descriptor->getTable()), "'").' AS '.addcslashes($db->protectIdentifiers($descriptor->getSingular()), "'").'\';');
		
		// HOOK: on_find
		$this->addPart($descriptor->getHookCode('on_find', false, '$q'));
		
		// TODO: Add autoloaded join-related handling code
		
		$this->addPart('return $q;');
	}
}


/* End of file PopulateFindQuery.php */
/* Location: ./lib/Db/Mapper/Part */