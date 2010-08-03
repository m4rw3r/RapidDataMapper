<?php
/*
 * Created by Martin Wernståhl on 2010-04-08.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_CreateSelectPart extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('createSelectPart');
		$this->setParamList('&$list, &$column_mappings');
		
		$db = $desc->getAdapter();
		
		$columns = array();
		foreach(array_merge($desc->getPrimaryKeys(), $desc->getColumns()) as $c)
		{
			$columns[] = $c->getDataType()->getSelectCode('$this->table_alias', "'");
			$map[] = '$column_mappings[] = $this->table_alias.\'.'.$c->getProperty().'\';';
		}
		
		$this->addPart('$list[] = '.implode(', \'.', $columns).'\';');
		$this->addPart(implode("\n", $map));
		
		$this->addPart('foreach($this->with as $join_alias => $join)
{
	$join->createSelectPart($list, $column_mappings);
}');
	}
}


/* End of file CreateSelectPart.php */
/* Location: ./lib/Rdm/Builder/Collection */