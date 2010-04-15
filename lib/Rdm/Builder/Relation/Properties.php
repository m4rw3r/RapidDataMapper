<?php
/*
 * Created by Martin Wernståhl on 2010-04-09.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Relation_Properties extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$this->addPart('public $id = '.$rel->getConstantIdentifier().';');
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('type', $rel->getType()));
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('reverse', false));
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('parent_object'));
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('alias'));
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('parent_alias'));
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('db', null, 'protected'));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'properties';
	}
}


/* End of file Properties.php */
/* Location: ./lib/Rdm/Builder/Relation */