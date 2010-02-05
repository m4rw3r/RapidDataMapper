<?php
/*
 * Created by Martin Wernståhl on 2010-01-15.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the code which handles allowDelete() for the object type parameter.
 */
class Db_Mapper_Part_CascadeDelete_IsQuery extends Db_CodeBuilder_Container
{
	protected $class;
	
	function __construct(Db_Descriptor $descriptor)
	{
		$this->class = $descriptor->getClass();
		
		$db = $descriptor->getConnection();
		
		foreach($descriptor->getRelations() as $rel)
		{
			if($rel->getOnDeleteAction() == Db_Descriptor::CASCADE)
			{
				$pk_cols = array();
				
				foreach($descriptor->getPrimaryKeys() as $pk)
				{
					$pk_cols[] = $descriptor->getSingular().'-'.$rel->getName().'.'.$pk->getColumn();
				}
				
				// $object is a query object which has fetched all the ids of the parent object(s)
				// $object needs to be wrapped in a Select object which fetches the ids of the current object(s)
				
				$this->addPart('// Relation '.$rel->getName().'
$q = $this->db->select();
$q->from(array(\''.$descriptor->getSingular().'\' => $object));

// Fill empty props to prevent notices
$q->php_columns = $q->sql_columns = array();

$this->joinRelated($q, \''.$rel->getName().'\', \''.$descriptor->getSingular().'\');
// Overwrite the usual columns
$q->columns = array(\''.addcslashes($db->protectIdentifiers(implode(', ', $pk_cols)), "'").'\');');

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
		$str = 'elseif($object instanceof Db_Query_Select)
{';
		
		$str .= self::indentCode("\n".implode("\n\n", $this->content));
		
		return $str."\n}";
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'query';
	}
}


/* End of file IsObject.php */
/* Location: ./lib/Db/Mapper/Part/AllowDelete */