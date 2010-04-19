<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Flush extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('flush');
		$this->setStatic(true);
		$this->setParamList('$private_flush = false');
		
		$this->addPart('if( ! $private_flush)
{
	return parent::flush();
}
else
{
	return self::$unit_of_work->commit();
}');
	}
}


/* End of file Flush.php */
/* Location: ./lib/Rdm/Builder/Collection */