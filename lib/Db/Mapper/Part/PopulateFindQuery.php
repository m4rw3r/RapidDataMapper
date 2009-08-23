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
	protected $descriptor;
	
	function __construct(Db_Descriptor $desc)
	{
		$this->name = 'populateFindQuery';
		
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
		$db = $this->descriptor->getConnection();
		
		$this->addPart('$q = new Db_Query_MapperSelect($this, \''.$this->descriptor->getSingular().'\');');
		
		$col_arr = array();
		foreach(array_merge($this->descriptor->getColumns(), $this->descriptor->getPrimaryKeys()) as $col)
		{
			$col_arr[] = $col->getSelectCode($this->descriptor->getSingular(), $this->descriptor->getSingular(), $db);
		}
		
		$this->addPart('$q->columns[] = \''.addcslashes(implode(', ', $col_arr), "'").'\';
$q->from[] = \''.addcslashes($db->protectIdentifiers($this->descriptor->getTable()), "'").' AS '.addcslashes($db->protectIdentifiers($this->descriptor->getSingular()), "'").'\';');
		
		// TODO: Add on_find hook here
		
		// TODO: Add autoloaded join-related handling code
		
		$this->addPart('return $q;');
	}
}


/* End of file PopulateFindQuery.php */
/* Location: ./lib/Db/Mapper/Part */