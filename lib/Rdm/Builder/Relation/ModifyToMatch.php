<?php
/*
 * Created by Martin Wernståhl on 2010-04-09.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Relation_ModifyToMatch extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$this->setMethodName('modifyToMatch');
		$this->setParamList('$object');
		$this->setPhpDoc('Internal: Modifies the supplied object so that it relates to the parent object.

@internal
@return void');
		
		list($local_keys, $foreign_keys) = $rel->getKeys();
		
		// $this->parent_object is the local object
		$modcode = array();
		
		// $object is the local object
		$reverse_code = array();
		while( ! empty($local_keys))
		{
			$local   = array_shift($local_keys);
			$foreign = array_shift($foreign_keys);
			
			if($rel->getType() == Rdm_Descriptor::HAS_ONE OR $rel->getType() == Rdm_Descriptor::HAS_MANY)
			{
				$reverse[] = $foreign->getAssignToObjectCode('$this->parent_object', $local->getFetchFromObjectCode('$object'));
				$modcode[] = $foreign->getAssignToObjectCode('$object', $local->getFetchFromObjectCode('$this->parent_object'));
			}
			else
			{
				$reverse[] = $local->getAssignToObjectCode('$object', $foreign->getFetchFromObjectCode('$this->parent_object'));
				$modcode[] = $local->getAssignToObjectCode('$this->parent_object', $foreign->getFetchFromObjectCode('$object'));
			}
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
}


/* End of file ModifyToMatch.php */
/* Location: ./lib/Rdm/Builder/Relation */