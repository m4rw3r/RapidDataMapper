<?php
/*
 * Created by Martin Wernståhl on 2010-04-09.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Relation_ToString extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$this->setMethodName('__toString');
		$this->setPhpDoc('Internal: Creates the relationship condition used in the ON part of JOIN if
no $parent_object is present, otherwise it filters '.$desc->getClass().' by '.$rel->getName().'.

@return string');
		
		$db = $desc->getAdapter();
		list($local_keys, $foreign_keys) = $rel->getKeys();
		
		$columns = array();
		$object_filter = array();
		while( ! empty($local_keys))
		{
			list($local, $foreign) = array(array_shift($local_keys), array_shift($foreign_keys));
			
			$columns[] = '$this->parent_alias.\'.'.addcslashes($db->protectIdentifiers($local->getColumn()), "'").' = \'.$this->alias.\'.'.addcslashes($db->protectIdentifiers($foreign->getColumn()), "'").'\'';
			$object_filter[] = '$this->alias.\'.'.addcslashes($db->protectIdentifiers($local->getColumn()), "'").' = \'.$this->db->escape('.$foreign->getFetchFromObjectCode('$this->parent_object').')';
		}
		
		$this->addPart('if($this->parent_object)
{
	return '.implode('.', $object_filter).';
}
else
{
	return '.implode('.', $columns).';
}');
	}
}


/* End of file ToString.php */
/* Location: ./lib/Rdm/Builder/Relation */