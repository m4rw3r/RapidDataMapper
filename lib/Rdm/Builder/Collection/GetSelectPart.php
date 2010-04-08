<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_GetSelectPart extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('getSelectPart');
		$this->setParamList('$this_alias, $alias_np');
		
		$db = $desc->getAdapter();
		
		$columns = array();
		foreach(array_merge($desc->getPrimaryKeys(), $desc->getColumns()) as $c)
		{
			$columns[] = '$this_alias.\'.'.$db->protectIdentifiers($c->getColumn()).' AS \'.$alias_np.\'_'.$c->getProperty();
		}
		
		$this->addPart('$select = array('.implode(', \'.', $columns).'\');');
		
		$this->addPart('foreach($this->with as $alias => $join)
{
	$select[] = $join->getSelectPart($this->db->protectIdentifiers($alias_np.\'_\'.$alias), $alias_np.\'_\'.$alias);
}');
		
		$this->addPart('return $select;');
	}
}


/* End of file GetSelectPart.php */
/* Location: ./lib/Rdm/Builder/Collection */