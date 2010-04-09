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
		$this->setPhpDoc('Internal.');
		
		list($local_keys, $foreign_keys) = $rel->getKeys();
		$modcode = array();
		while( ! empty($local_keys))
		{
			list($local, $foreign) = array(array_shift($local_keys), array_shift($foreign_keys));
			
			$modcode[] = $foreign->getAssignToObjectCode('$object', $local->getFetchFromObjectCode('$this->parent_object'));
		}
		
		$this->addPart(implode("\n\t", $modcode));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'properties';
	}
}


/* End of file ModifyToMatch.php */
/* Location: ./lib/Rdm/Builder/Relation */