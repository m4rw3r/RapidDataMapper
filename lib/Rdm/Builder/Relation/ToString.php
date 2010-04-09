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
		$this->setPhpDoc('Internal.');
		
		$db = $desc->getAdapter();
		list($local_keys, $foreign_keys) = $rel->getKeys();
		
		$columns = array();
		while( ! empty($local_keys))
		{
			list($local, $foreign) = array(array_shift($local_keys), array_shift($foreign_keys));
			
			$columns[] = '$this->parent_alias.\'.'.addcslashes($db->protectIdentifiers($local->getColumn()), "'").' = \'.$this->alias.\'.'.addcslashes($db->protectIdentifiers($foreign->getColumn()), "'").'\'';
		}
		
		$this->addPart('return '.implode('.', $columns).';');
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'properties';
	}
}


/* End of file ToString.php */
/* Location: ./lib/Rdm/Builder/Relation */