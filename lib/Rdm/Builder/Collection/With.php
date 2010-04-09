<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_With extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('with');
		$this->setParamList('$relation_id');
		
		if(count($desc->getRelations()))
		{
			$first = true;
			
			foreach($desc->getRelations() as $r)
			{
				$this->addPart(new Rdm_Builder_Collection_With_RelationCase($r, $desc, $first));
				
				$first = false;
			}
			
			$this->addPart('throw new Exception(\'No matching relation can be found for the class "'.$desc->getClass().'"\');');
		}
		else
		{
			$this->addPart('throw new Exception(\'No relations can be found for the class "'.$desc->getClass().'"\');');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		return parent::__toString();
	}
}


/* End of file With.php */
/* Location: ./lib/Rdm/Builder/Collection */