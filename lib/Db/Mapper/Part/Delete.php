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
		
		$db = $descriptor->getConnection();
		
		// HOOK: on_delete
		$this->addPart($descriptor->getHookCode('on_delete', '$object'));
		
		// is it already deleted?
		$this->addPart('if($object instanceof '.$descriptor->getClass().' && empty($object->__id))
{
	return false;
}');
		
		// Check if we are allowed to delete this row
		$this->addPart('if( ! $this->allowDelete($object))
{
	return false;
}');
		
		// Run the cascades
		$this->addPart('if( ! $this->cascadeDelete($object))
{
	return false;
}');
		
		// TODO: Call the unlink relation code for the relations which haven't been affected by the cascades
		
		// TODO: Add plugin support for extra deletes
		
		$filter = array();
		foreach($descriptor->getPrimaryKeys() as $key)
		{
			$filter[] = $db->protectIdentifiers('td.'.$key->getColumn().' = rel.'.$key->getColumn());
		}
		
		$this->addPart('if($object instanceof '.$descriptor->getClass().')
{
	$ret = $this->db->delete(\''.$descriptor->getTable().'\', $object->__id);
}
else
{
	$ret = $this->db->delete(array(\'td\' => \''.$descriptor->getTable().'\'))
		->from(array(\'rel\' => $object))
		->escape(false)->where(\''.implode('AND', $filter).'\')
		->execute();
}');
		
		// HOOK: post_delete
		$this->addPart($descriptor->getHookCode('post_delete', '$object', '$ret'));
		
		$this->addPart('if($object instanceof '.$descriptor->getClass().' && $ret)
{
	$object->__id = array();
}');
		
		$this->addPart('return $ret;');
	}
}


/* End of file Delete.php */
/* Location: ./lib/Db/Mapper/Part */