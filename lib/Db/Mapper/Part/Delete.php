<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the delete() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_Delete extends Db_CodeBuilder_Method
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = 'delete';
		$this->param_list = '$object';
		
		// HOOK: on_delete
		$this->addPart($descriptor->getHookCode('on_delete', '$object'));
		
		// is it already deleted?
		$this->addPart('if(empty($object->__id))
{
	return false;
}');
		
		// Check if we are allowed to delete this row
		$this->addPart('if( ! $this->allowDelete($object))
{
	return false;
}');
		
		// TODO: Add delete cascade
		
		// TODO: Call the unlink relation code for the relations which haven't been affected by the cascades
		
		$this->addPart('$ret = $this->db->delete(\''.$descriptor->getTable().'\', $object->__id);');
		
		// HOOK: post_delete
		$this->addPart($descriptor->getHookCode('post_delete', '$object', '$ret'));
		
		$this->addPart('if($ret)
{
	$object->__id = array();
}');
		
		$this->addPart('return $ret;');
	}
}


/* End of file Delete.php */
/* Location: ./lib/Db/Mapper/Part */