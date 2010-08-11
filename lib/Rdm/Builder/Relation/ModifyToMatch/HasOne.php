<?php
/*
 * Created by Martin Wernståhl on 2010-04-09.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Relation_ModifyToMatch_HasOne extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor_Relation $rel)
	{
		list($local_keys, $foreign_keys) = $rel->getKeys();
		
		// $this->parent_object is the local object
		$modcode = array();
		
		// $object is the local object
		$reverse_code = array();
		
		// Set the object too
		$modcode[] = '$object->'.$rel->getProperty().' = $this->parent_object;';
		$reverse[] = '$this->parent_object->'.$rel->getProperty().' = $object;';
		
		while( ! empty($local_keys))
		{
			$local   = array_shift($local_keys);
			$foreign = array_shift($foreign_keys);
			
			$modcode[] = $foreign->getAssignToObjectCode('$object', $local->getFetchFromObjectCode('$this->parent_object'));
			$reverse[] = $foreign->getAssignToObjectCode('$this->parent_object', $local->getFetchFromObjectCode('$object'));
		}
		
		$this->addPart('if($this->reverse)
{
	'.implode("\n\t", $reverse).'
}
else
{
	'.implode("\n\t", $modcode).'
}');
	}
	
	public function getName()
	{
		return 'has_one';
	}
}


/* End of file HasOne.php */
/* Location: ./lib/Rdm/Builder/Relation/ModifyToMatch */