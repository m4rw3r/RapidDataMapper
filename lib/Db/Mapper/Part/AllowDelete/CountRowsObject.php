<?php
/*
 * Created by Martin Wernståhl on 2010-01-15.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the code which handles allowDelete() for the object type parameter.
 */
class Db_Mapper_Part_AllowDelete_CountRowsObject extends Db_CodeBuilder_Container
{
	function __construct(Db_Descriptor $descriptor)
	{
		foreach($descriptor->getRelations() as $rel)
		{
			if($rel->getOnDeleteAction() == Db_Descriptor::RESTRICT)
			{
				$this->addPart('// relation '.$rel->getName().'
$q = $this->db->select();
// add count, no need to escape
$q->escape(false)->from(array(\''.$rel->getRelatedDescriptor()->getSingular().'\' => \''.$rel->getRelatedDescriptor()->getTable().'\'), \'COUNT(1)\');
$this->applyRelatedConditions($q, \''.$rel->getName().'\', $object);
// Hack to get the WHERE part into a normal query:
$q->where[] = preg_replace(\'/(?:\s+AND|\s+OR)?[\( ]*$/i\', \'\', $q->where_prefix);');
				
				$this->addPart('if($q->get()->val())
{
	return false;
}');
			}
		}
		
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'count';
	}
}


/* End of file CountRowsObject.php */
/* Location: ./lib/Db/Mapper/Part/AllowDelete */