<?php
/*
 * Created by Martin Wernståhl on 2010-01-15.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the code which handles allowDelete() for the object type parameter.
 */
class Db_Mapper_Part_CascadeDelete_IsObject extends Db_CodeBuilder_Container
{
	protected $class;
	
	function __construct(Db_Descriptor $descriptor)
	{
		$this->class = $descriptor->getClass();
		
		foreach($descriptor->getRelations() as $rel)
		{
			if($rel->getOnDeleteAction() == Db_Descriptor::CASCADE)
			{
				$pk_cols = array();
				
				foreach($descriptor->getPrimaryKeys() as $pk)
				{
					$pk_cols[] = $pk->getColumn();
				}
				
				$this->addPart('// Relation '.$rel->getName().'
$q = $this->db->select();
// Add select of primary keys
$q->from(array(\''.$rel->getRelatedDescriptor()->getSingular().'\' => \''.$rel->getRelatedDescriptor()->getTable().'\'), \''.implode(', ', $pk_cols).'\');
$this->applyRelatedConditions($q, \''.$rel->getName().'\', $object);
// Hack to get the WHERE part into a normal query:
$q->where[] = preg_replace(\'/(?:\s+AND|\s+OR)?[\( ]*$/i\', \'\', $q->where_prefix);');

				$this->addPart('// Create count query
$q_count = $this->db->select()->escape(false)->from($q, \'COUNT(1)\');');
				
				$this->addPart('if($q_count->get()->val())
{
	// Go deeper in the relation tree
	$m = Db::getMapper(\''.$rel->getRelatedDescriptor()->getClass().'\');
	
	if( ! $m->delete($q))
	{
		return false;
	}
}');
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$str = 'if($object instanceof '.$this->class.")\n{";
		
		$str .= self::indentCode("\n".implode("\n\n", $this->content));
		
		return $str."\n}";
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'object';
	}
}


/* End of file IsObject.php */
/* Location: ./lib/Db/Mapper/Part/AllowDelete */