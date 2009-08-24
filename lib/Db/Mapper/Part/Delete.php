<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the delete() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_Delete extends Db_Mapper_Code_Method
{
	protected $descriptor;
	
	function __construct(Db_Descriptor $desc)
	{
		$this->name = 'delete';
		$this->param_list = '$object';
		
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
		// HOOK: on_delete
		$this->addPart($this->descriptor->getHookCode('on_delete', '$object'));
		
		// is it already deleted?
		$this->addPart('if(empty($object->__id))
{
	return false;
}');
		
		// TODO: Add calls to cascades
		
		// TODO: Call the unlink relation code for the relations which haven't been affected by the cascades
		
		$this->addPart('$ret = $this->db->delete(\''.$this->descriptor->getTable().'\', $object->__id);');
		
		// HOOK: on_delete
		$this->addPart($this->descriptor->getHookCode('post_delete', '$object'));
		
		$this->addPart('return $ret;');
	}
}


/* End of file Delete.php */
/* Location: ./lib/Db/Mapper/Part */