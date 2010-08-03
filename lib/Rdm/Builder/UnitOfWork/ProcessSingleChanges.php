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
				$arr[] = '$e->__data[\''.$c->getColumn().'\'] !== '.$c->getFetchFromObjectCode('$e').' && $data[] = \''.addcslashes($db->protectIdentifiers($c->getColumn()), "'").' = '.$c->getDataType()->getSqlValueCode($c->getFetchFromObjectCode('$e'), "'").';';
			}
		}
		
		$pks = array();
		foreach($desc->getPrimaryKeys() as $k)
		{
			$pks[] = addcslashes($db->protectIdentifiers($k->getColumn()), "'").' = \'.$this->db->escape($e->__id[\''.$k->getColumn().'\'])';
		}
		
		$this->addPart('foreach($this->modified as $e)
{
	$data = array();
	
	'.implode("\n\t", $arr).'
	
	if( ! empty($data))
	{
		$this->db->query(\'UPDATE '.addcslashes($db->protectIdentifiers($db->dbprefix.$desc->getTable()), "'").' SET \'.implode(\', \', $data).\' WHERE '.implode('.\' AND ', $pks).');
	}
}');
	}
}


/* End of file ProcessSingleChanges.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork */