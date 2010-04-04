<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_UnitOfWork_ProcessSingleChanges extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('processSingleChanges');
		$this->setPublic(false);
		
		$db = $desc->getAdapter();
		
		$this->addPart('$to_update = array();');
		
		$arr = array();
		
		foreach($desc->getColumns() as $c)
		{
			if($c->isUpdatable())
			{
				$arr[] = '$e->__data[\''.$c->getColumn().'\'] !== '.$c->getFromObjectCode('$e').' && $data[] = \''.addcslashes($db->protectIdentifiers($c->getColumn()), "'").' = \'.$this->db->escape('.$c->getFromObjectToSetSQLValueCode('$e').');';
			}
		}
		
		$pks = array();
		foreach($desc->getPrimaryKeys() as $k)
		{
			$pks[] = addcslashes($db->protectIdentifiers($k->getColumn()), "'").' = \'.$this->db->escape($e->__id[\''.$k->getColumn().'\'])';
		}
		
		$this->addPart('foreach($this->entities as $e)
{
	$data = array();
	
	'.implode("\n", $arr).'
	
	if( ! empty($data))
	{
		// TODO: Perform queries here instead
		var_dump(\'UPDATE '.addcslashes($db->protectIdentifiers($desc->getTable()), "'").' SET \'.implode(\', \', $data).\' WHERE '.implode('.\' AND ', $pks).');
	}
}');
		
		// TODO: Call queries instead of dumping them
	}
}


/* End of file ProcessSingleChanges.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork */