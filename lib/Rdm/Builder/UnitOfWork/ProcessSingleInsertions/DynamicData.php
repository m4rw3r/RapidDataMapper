<?php
/*
 * Created by Martin Wernståhl on 2010-04-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Variant of the insertion code which performs one insert per new row and then
 * fetches database generated data to populate the objects.
 */
class Rdm_Builder_UnitOfWork_ProcessSingleInsertions_DynamicData extends Rdm_Util_Code_Container
{
	function __construct(Rdm_Descriptor $desc)
	{
		$db = $desc->getAdapter();
		
		$pre = array();
		$assignments = array();
		$loaded_column_selects = array();
		$loaded_column_assignments = array();
		$post = array();
		foreach(array_merge($desc->getPrimaryKeys(), $desc->getColumns()) as $c)
		{
			// Get validation or population code
			$code = trim($c->getPreInsertCode('$data', '$entity'));
			
			if( ! empty($code))
			{
				$pre[] = $code;
			}
			
			if($c->isInsertable())
			{
				$columns[] = $db->protectIdentifiers($c->getColumn());
				$assignments[] = $c->getFromObjectToDataCode('$entity', '$data');
			}
			
			if($c->getLoadAfterInsert() === Rdm_Descriptor::PLAIN_COLUMN)
			{
				// Plain column to fetch from the database
				$loaded_column_selects[] = $c->getSelectCode($desc->getSingular(), $desc->getSingular(), $db);
				$loaded_column_assignments[] = $c->getFromDataToObjectCode('$event', '$udata', '$prefix');
			}
			
			// Special logic for the column
			$code = $c->getPostInsertCode('$entity');
			
			if( ! empty($code))
			{
				$post[] = $code;
			}
		}
		
		$pks = array();
		foreach($desc->getPrimaryKeys() as $k)
		{
			$pks[] = addcslashes($db->protectIdentifiers($k->getColumn()), "'").' = \'.$this->db->escape($entity->__id[\''.$k->getColumn().'\'])';
		}
		
		
		$str = 'foreach($this->new_entities as $entity)
{
	$data = array();
	
	'.implode("\n\t", $assignments);
		
		if( ! empty($pre))
		{
			$str .= "\n\n\t".implode("\n\t", $pre);
		}
		
		$str .= '
	
	$this->db->query(\'INSERT INTO '.addcslashes($db->protectIdentifiers($db->dbprefix.$desc->getTable()), '\'').' ('.addcslashes(implode(', ', $columns), "'").') VALUES (\'.implode(\', \', array_map(array($this->db, \'escape\'), $data)).\')\');';
		
		if( ! empty($post))
		{
			$str .= "\n\n\t".implode("\n\t", $post);
		}
		
		if( ! empty($loaded_column_selects))
		{
			$str .= "\n\n\t".'$this->db->query(\'SELECT '.addcslashes(implode(', ', $loaded_column_selects), "'").' FROM '.addcslashes($db->protectIdentifiers($db->dbprefix.$desc->getTable()), "'").' AS '.addcslashes($db->protectIdentifiers($desc->getSingular()), "'").' WHERE '.implode('.\' AND ', $pks).');
	
	$prefix = \''.$desc->getSingular().'\';';
		}
		
		$this->addPart($str.'
}');
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'dynamic';
	}
}


/* End of file DynamicData.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork/ProcessSingleInsertions */