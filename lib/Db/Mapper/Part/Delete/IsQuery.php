<?php
/*
 * Created by Martin Wernståhl on 2010-01-15.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the code which handles allowDelete() for the object type parameter.
 */
class Db_Mapper_Part_Delete_IsQuery extends Db_CodeBuilder_Container
{
	function __construct(Db_Descriptor $descriptor)
	{
		$db = $descriptor->getConnection();
		
		foreach($descriptor->getRelations() as $rel)
		{
			if($rel->getOnDeleteAction() == Db_Descriptor::SET_NULL)
			{
				
			}
		}
		
		// Build the filter which will delete the objects determined by the subquery
		$filter = array();
		foreach($descriptor->getPrimaryKeys() as $key)
		{
			$filter[] = $db->protectIdentifiers('td.'.$key->getColumn().' = rel.'.$key->getColumn());
		}
		
		$this->addPart('$ret = $this->db->delete(array(\'td\' => \''.$descriptor->getTable().'\'))
	->from(array(\'rel\' => $object))
	->escape(false)->where(\''.implode('AND', $filter).'\')
	->execute();');
		
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$str = 'else
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