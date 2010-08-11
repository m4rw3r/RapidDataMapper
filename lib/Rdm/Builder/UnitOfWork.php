<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_UnitOfWork extends Rdm_Util_Code_ClassBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setClassName($desc->getUnitOfWorkClassName());
		$this->setExtends(($desc->isNamespaced() ? '\\' : '').'Rdm_UnitOfWork');
		
		$this->addPart(new Rdm_Builder_UnitOfWork_EstablishRelationLinks($desc));
		$this->addPart(new Rdm_Builder_UnitOfWork_ProcessSingleInserts($desc));
		$this->addPart(new Rdm_Builder_UnitOfWork_ProcessSingleChanges($desc));
		$this->addPart(new Rdm_Builder_UnitOfWork_ProcessSingleDeletes($desc));
		$this->addPart(new Rdm_Builder_UnitOfWork_UpdateShadowData($desc));
	}
}


/* End of file UnitOfWork.php */
/* Location: ./lib/Rdm/Builder */