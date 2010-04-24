<?php
/*
 * Created by Martin Wernståhl on 2010-04-24.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_CreateSelectCountPart extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('createSelectCountPart');
		$this->setParamList('$from');
		
		$db = $desc->getAdapter();
		
		// TODO: Check if there is another syntax for multi-column DISTINCT
		$columns = array();
		foreach($desc->getPrimaryKeys() as $c)
		{
			$columns[] = '$this->table_alias.\'.'.addcslashes($db->protectIdentifiers($c->getColumn()), "'");
		}
		
		// TODO: Come up with a generic workaround for SQLite and the like who do not support the COUNT DISTINCT combo
		if($db instanceof Rdm_Adapter_SQLite)
		{
			$this->addPart('return \'SELECT COUNT(\'.'.implode(' + \\\'|\\\' + \'.', $columns).') FROM (SELECT DISTINCT \'.'.implode(', ', $columns).'\'.$from.\') AS \'.$this->table_alias;');
		}
		else
		{
			$this->addPart('return \'SELECT COUNT(DISTINCT \'.'.implode(' + \\\'|\\\' + \'.', $columns).')\'.$from;');
		}
	}
}


/* End of file CreateSelectCountPart.php */
/* Location: ./lib/Rdm/Builder/Collection */