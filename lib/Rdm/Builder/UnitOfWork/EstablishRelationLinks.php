<?php
/*
 * Created by Martin Wernståhl on 2010-07-12.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_UnitOfWork_EstablishRelationLinks extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('establishRelationLinks');
		$this->setPublic(false);
		
		foreach($desc->getRelations() as $rel)
		{
			foreach($rel->getEstablishCodeParts() as $part)
			{
				$this->addPart($part);
			}
		}
	}
}


/* End of file EstablishRelationLinks.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork */