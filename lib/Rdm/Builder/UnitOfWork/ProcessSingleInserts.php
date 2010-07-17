<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_UnitOfWork_ProcessSingleInserts extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('processSingleInserts');
		$this->setPublic(false);
		
		// TODO: Add an if which chooses if to use the dynamic id fetcher (ie. a piece of generated code that runs one query per inserted row and then fetches the auto generated id column) or the static id inserter (which does one query for all rows)
		$this->addPart(new Rdm_Builder_UnitOfWork_ProcessSingleInserts_DynamicData($desc));
	}
}


/* End of file ProcessSingleInserts.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork */