<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Create_FromRelatedEntity extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$this->setMethodName('createFrom'.ucfirst($rel->getName()));
		$this->setStatic(true);
		$this->setPhpDoc('Returns a new '.$desc->getCollectionClassName(true, true).' instance which filters its entities by the supplied '.$rel->getRelatedDescriptor()->getClass(true, true).' entity.

@param  '.$rel->getRelatedDescriptor()->getClass(true, true).'
@return '.$desc->getCollectionClassName());
		$this->setParamList($rel->getRelatedDescriptor()->getClass(true, true).' $related_entity');
		
		$this->addPart('$c = new '.$desc->getCollectionClassName().';');
		
		$this->addPart('$c->has()->related'.ucfirst($rel->getName()).'($related_entity);');
		
		$this->addPart('return $c;');
	}
}


/* End of file Plain.php */
/* Location: ./lib/Rdm/Builder/Collection/Create */