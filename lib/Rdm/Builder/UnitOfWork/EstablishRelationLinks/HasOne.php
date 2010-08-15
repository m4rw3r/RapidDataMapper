<?php
/*
 * Created by Martin Wernståhl on 2010-07-12.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_UnitOfWork_EstablishRelationLinks_HasOne extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor_Relation $rel)
	{
		list($local_keys, $foreign_keys) = $rel->getKeys();
		
		$conditions = array();
		$sets = array();
		while( ! empty($local_keys))
		{
			$local = array_shift($local_keys);
			$foreign = array_shift($foreign_keys);
			
			$conditions[] = $local->getFetchFromObjectCode('$entity').' === '.$foreign->getFetchFromObjectCode('$entity->'.$rel->getProperty());
			
			// foreign = local
			$sets[] = $foreign->getAssignToObjectCode('$entity->'.$rel->getProperty(), $local->getFetchFromObjectCode('$entity'));
		}
		
		$this->addPart('foreach(array_merge($this->modified, $this->new_entities) as $entity)
{
	if(isset($entity->'.$rel->getProperty().') && ! ('.implode(' && ', $conditions).'))
	{
		// TODO: Issue query which will set NULL or delete all other related rows
		'.self::indentCode(self::indentCode(implode("\n", $sets))).'
	}
}');
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'has_one';
	}
}


/* End of file HasOne.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork/EstablishRelationLinks */