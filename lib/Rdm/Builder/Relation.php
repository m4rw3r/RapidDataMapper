<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Relation extends Rdm_Util_Code_ClassBuilder
{
	public function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$this->setClassName($rel->getRelationFilterClassName());
		$this->setImplements(($desc->isNamespaced() ? '\\' : '').'Rdm_Collection_FilterInterface');
		$this->setPhpDoc('A class which handles relations between '.$desc->getClass().' and '.$rel->getName().': it can produce join conditions
for '.$desc->getClass().' to '.$rel->getName().', filter '.$desc->getClass().' by '.$rel->getName().' and finally establish a relation between '.$desc->getClass().' and '.$rel->getName().'.');
		
		$this->addPart(new Rdm_Builder_Relation_Properties($rel, $desc));
		
		$this->addPart(new Rdm_Builder_Relation_Construct($rel, $desc));
		
		$this->addPart(new Rdm_Builder_Relation_Methods($rel, $desc));
		
		$this->addPart(new Rdm_Builder_Relation_ModifyToMatch($rel, $desc));
		$this->addPart(new Rdm_Builder_Relation_ModifyToNotMatch($rel, $desc));
		
		$this->addPart(new Rdm_Builder_Relation_ToString($rel, $desc));
	}
}


/* End of file Relation.php */
/* Location: ./lib/Rdm/Builder */