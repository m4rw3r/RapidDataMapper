<?php
/*
 * Created by Martin Wernståhl on 2010-04-14.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_CollectionFilter_FilterRelatedCollectionMethod extends Rdm_Util_Code_MethodBuilder
{
	function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$this->setMethodName('related'.ucfirst($rel->getName()).'Collection');
		$this->setParamList($rel->getRelatedDescriptor()->getCollectionClassName(true, true).' $object');
		
		$db = $desc->getAdapter();
		list($local_keys, $foreign_keys) = $rel->getKeys();
		$filter_cols = $select_cols = array();
		
		while( ! empty($local_keys))
		{
			list($local, $foreign) = array(array_shift($local_keys), array_shift($foreign_keys));
			
			$filter_cols[] = '$this->table_alias.\'.'.$db->protectIdentifiers($local->getColumn());
			$select_cols[] = '$object->table_alias.\'.'.$db->protectIdentifiers($foreign->getColumn());
		}
		
		$this->addPart('$this->is_dynamic = true;
empty($this->filters) OR $this->filters[] = \'AND\';
$this->filters[] = \'(\'.'.implode(', \'.', $filter_cols).') IN (\'.$object->createSelectColumnsQuery('.implode(', \'.', $select_cols).'\').\')\';');
		
		$this->addPart('return $this;');
	}
}


/* End of file FilterRelatedCollectionMethod.php */
/* Location: ./lib/Rdm/Builder/CollectionFilter */