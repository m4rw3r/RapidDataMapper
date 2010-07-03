<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_SetUnitOfWork extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('setUnitOfWork');
		$this->setStatic(true);
		$this->setParamList(($desc->isNamespaced() ? '\\' : '').'Rdm_UnitOfWork $u');
		
		$this->addPart('self::$unit_of_work = $u;');
	}
}


/* End of file SetUnitOfWork.php */
/* Location: ./lib/Rdm/Builder/Collection */