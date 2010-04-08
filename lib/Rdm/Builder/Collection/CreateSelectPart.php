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
		$this->setParamList('$parent_alias, &$list, &$column_mappings');
		
		$db = $desc->getAdapter();
		
		$this->addPart('$alias = $parent_alias ? $parent_alias : \''.$desc->getSingular().'\';');
		
		$columns = array();
		foreach(array_merge($desc->getPrimaryKeys(), $desc->getColumns()) as $c)
		{
			$columns[] = '$alias.\'.'.addcslashes($db->protectIdentifiers($c->getColumn()), "'");
			$map[] = '$column_mappings[] = $alias.\'.'.$c->getProperty().'\';';
		}
		
		$this->addPart('$list[] = '.implode(', \'.', $columns).'\';');
		$this->addPart(implode("\n", $map));
		
		$this->addPart('foreach($this->with as $join_alias => $join)
{
	$join->createSelectPart($join_alias, $list, $column_mappings);
}');
	}
}


/* End of file CreateSelectPart.php */
/* Location: ./lib/Rdm/Builder/Collection */