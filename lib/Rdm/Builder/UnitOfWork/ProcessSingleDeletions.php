<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_UnitOfWork_ProcessSingleDeletions extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('processSingleDeletions');
		$this->setPublic(false);
		
		// TODO: Implement support for ON DELETE
		
		$db = $desc->getAdapter();
		
		$this->addPart('$ids = array();');
		
		$pks = array();
		foreach($desc->getPrimaryKeys() as $k)
		{
			$pks[] = $db->protectIdentifiers($k->getColumn());
		}
		
		$this->addPart('foreach($this->deleted_entities as $e)
{
	$ids[] = \'(\'.implode(\', \', array_map(array($this->db, \'escape\'), $e->__id)).\')\';
}

if( ! empty($ids))
{
	$this->db->query(\'DELETE FROM '.addcslashes($db->protectIdentifiers($db->dbprefix.$desc->getTable()), "'").' WHERE ('.addcslashes(implode(', ', $pks), "'").') IN (\'.implode(\', \', $ids).\')\');
}');
	}
}


/* End of file ProcessSingleDeletions.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork */