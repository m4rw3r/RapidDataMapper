<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_RelationFilter extends Rdm_Util_Code_ClassBuilder
{
	public function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$this->setClassName($rel->getRelationFilterClassName());
		$this->setImplements('Rdm_Collection_FilterInterface');
		
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('id', $rel->getIntegerIdentifier()));
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('type', $rel->getType()));
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('parent_object'));
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('alias'));
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('parent_alias'));
		
		// TODO: WHAT HAPPENS IF WE ADD AN OWNING OBJECT TO A COLLECTION FILTERING ON OWNED OBJECTS? IS THE ASSIGNMENT GOING IN THE CORRECT DIRECTION? IS IT POSSIBLE? CAN WE OVERWRITE PRIMARY KEYS?!?!?!
		
		list($local_keys, $foreign_keys) = $rel->getKeys();
		$modcode = array();
		while( ! empty($local_keys))
		{
			list($local, $foreign) = array(array_shift($local_keys), array_shift($foreign_keys));
			
			$modcode[] = $foreign->getAssignToObjectCode('$object', $local->getFetchFromObjectCode('$this->parent_object'));
		}
		
		$this->addPart('public function setAliases($alias, $parent_alias)
{
	$this->alias = $alias;
	$this->parent_alias = $parent_alias;
}

public function canModifyToMatch()
{
	return ! empty($this->parent_object);
}

public function modifyToMatch($object)
{
	'.implode("\n\t", $modcode).'
}');
		
		$db = $desc->getAdapter();
		list($local_keys, $foreign_keys) = $rel->getKeys();
		
		$columns = array();
		while( ! empty($local_keys))
		{
			list($local, $foreign) = array(array_shift($local_keys), array_shift($foreign_keys));
			
			$columns[] = '$this->parent_alias.\'.'.addcslashes($db->protectIdentifiers($local->getColumn()), "'").' = \'.$this->alias.\'.'.addcslashes($db->protectIdentifiers($foreign->getColumn()), "'").'\'';
		}
		
		$this->addPart('public function __toString()
{
	return '.implode('.', $columns).';
}');
	}
}


/* End of file Collection.php */
/* Location: ./lib/Rdm/Builder */