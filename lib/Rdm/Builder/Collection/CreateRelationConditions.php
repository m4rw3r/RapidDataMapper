<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_CreateRelationConditions extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('createRelationConditions');
		$this->setParamList('$alias, $parent_alias, $relation_id');
		
		$db = $desc->getAdapter();
		
		$first = true;
		
		foreach($desc->getRelations() as $r)
		{
			$this->addPart(new Rdm_Builder_Collection_CreateRelationConditions_RelationCase($r, $desc, $first));
			
			$first = false;
		}
	}
}


/* End of file CreateRelationConditions.php */
/* Location: ./lib/Rdm/Builder/Collection */