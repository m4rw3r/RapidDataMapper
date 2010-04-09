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
		$this->setExtends('Rdm_UnitOfWork');
		
		$this->addPart(new Rdm_Util_Code_PropertyBuilder('adapter_name', $desc->getAdapterName(), 'protected'));
		
		$this->addPart(new Rdm_Builder_UnitOfWork_ProcessSingleInsertions($desc));
		$this->addPart(new Rdm_Builder_UnitOfWork_ProcessSingleChanges($desc));
		$this->addPart(new Rdm_Builder_UnitOfWork_ProcessSingleDeletions($desc));
		$this->addPart(new Rdm_Builder_UnitOfWork_UpdateShadowData($desc));
	}
}


/* End of file UnitOfWork.php */
/* Location: ./lib/Rdm/Builder */