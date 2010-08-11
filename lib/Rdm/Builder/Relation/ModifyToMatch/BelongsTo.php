<?php
/*
 * Created by Martin Wernståhl on 2010-04-09.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Relation_ModifyToMatch_BelongsTo extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor_Relation $rel)
	{
		list($local_keys, $foreign_keys) = $rel->getKeys();
		
		// $this->parent_object is the local object
		$modcode = array();
		
		// $object is the local object
		$reverse_code = array();
		
		// Set the object too
		$reverse[] = '$object->'.$rel->getProperty().' = $this->parent_object;';
		$modcode[] = '$this->parent_object->'.$rel->getProperty().' = $object;';
		
		while( ! empty($local_keys))
		{
			$local   = array_shift($local_keys);
			$foreign = array_shift($foreign_keys);
			
			$reverse[] = $local->getAssignToObjectCode('$object', $foreign->getFetchFromObjectCode('$this->parent_object'));
			$modcode[] = $local->getAssignToObjectCode('$this->parent_object', $foreign->getFetchFromObjectCode('$object'));
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
		return 'belongs_to';
	}
}


/* End of file BelongsTo.php */
/* Location: ./lib/Rdm/Builder/Relation/ModifyToMatch */