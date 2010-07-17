<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_DeleteAll extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('deleteAll');
		
		$db = $desc->getAdapter();
		$select_cols = array();
		$columns = array();
		foreach($desc->getPrimaryKeys() as $pk)
		{
			$select_cols[] = $db->protectIdentifiers($desc->getSingular().'.'.$pk->getColumn());
			$columns[] = $db->protectIdentifiers($db->dbprefix.$desc->getTable().'.'.$pk->getColumn());
		}
		$select_cols = implode(', ', $select_cols);
		$columns = implode(', ', $columns);
		
		$del_query = 'DELETE FROM '.$db->dbprefix.$desc->getTable().' WHERE ('.$columns.') IN ';
		
		$this->addPart('$this->is_locked = true;

$this->getUnitOfWorkInstance()->addCustomDelete(new Rdm_Collection_DeleteContentsOfCollectionOperation($this, '.var_export($select_cols, true).', '.var_export($del_query, true).'));

$c = $this->count();

return $c;');
	}
}


/* End of file DeleteAll.php */
/* Location: ./lib/Rdm/Builder/Collection */