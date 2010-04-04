<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_UnitOfWork_ProcessSingleInsertions extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('processSingleInsertions');
		$this->setPublic(false);
	}
}


/* End of file ProcessSingleInsertions.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork */