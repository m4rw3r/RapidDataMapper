<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_UnitOfWork_UpdateShadowData extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('updateShadowData');
		$this->setPublic(false);
		
		$arr = array('foreach($this->entities as $e)
{');
		
		foreach($desc->getColumns() as $p)
		{
			if($p->isUpdatable())
			{
				$arr[] = $p->getFromObjectToDataCode('$e', '$e->__data', true);
			}
		}
		
		count($arr) > 1 && $this->addPart(implode("\n\t", $arr).'
}');
	}
}


/* End of file UpdateShadowData.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork */