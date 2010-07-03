<?php
/*
 * Created by Martin Wernståhl on 2010-04-14.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_CollectionFilter_FilterRelatedObjectMethod extends Rdm_Util_Code_MethodBuilder
{
	function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$db       = $desc->getAdapter();
		
		$this->setMethodName('related'.ucfirst($rel->getName()));
		$this->setParamList($rel->getRelatedDescriptor()->getNamespace(true, true).$rel->getRelatedDescriptor()->getClass().' $object');
		
		$this->addPart('$this->modifiers[] = $o = new '.$rel->getRelationFilterClassName().'($object, $this->table_alias);
$this->filters[] = $o;');
		
		$this->addPart('return $this;');
	}
}


/* End of file FilterRelatedObjectMethod.php */
/* Location: ./lib/Rdm/Builder/CollectionFilter */