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
		$this->setImplements('Rdm_Collection_FilterInterface');
		
		$this->addPart(new Rdm_Builder_Relation_Properties($rel, $desc));
		
		$this->addPart(new Rdm_Builder_Relation_Methods($rel, $desc));
		
		$this->addPart(new Rdm_Builder_Relation_ModifyToMatch($rel, $desc));
		
		$this->addPart(new Rdm_Builder_Relation_ToString($rel, $desc));
	}
}


/* End of file Relation.php */
/* Location: ./lib/Rdm/Builder */