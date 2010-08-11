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
	public function __construct(Rdm_Descriptor_Relation $rel)
	{
		$this->setMethodName('modifyToMatch');
		$this->setParamList('$object');
		$this->setPhpDoc('Internal: Modifies the supplied object so that it relates to the parent object.

@internal
@return void');
		
		$c = $rel->getModifyToMatchCode();
		
		empty($c) OR $this->addPart($c);
	}
}


/* End of file ModifyToMatch.php */
/* Location: ./lib/Rdm/Builder/Relation */