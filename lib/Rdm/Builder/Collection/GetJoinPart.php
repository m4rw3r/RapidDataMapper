<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_GetJoinPart extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('getJoinPart');
		$this->setParamList('$this_alias, $alias_np, $parent_alias');
		
		$db = $desc->getAdapter();
		
		$this->addPart('$type = empty($this->filters) ? \'LEFT \' : \'INNER \';');
		
		$this->addPart('$extra = empty($this->filters) ? \'\' : \' AND \'.implode(\' \', $this->filters);');
		
		$this->addPart('$arr = array($type.\'JOIN '.addcslashes($db->protectIdentifiers($desc->getTable()), "'").' AS \'.$this_alias.\' ON \'.$this->parent->createRelationConditions($this_alias, $parent_alias, $this->relation_id).$extra);

foreach($this->with as $alias => $join)
{
	$arr += $join->getJoinPart($this->db->protectIdentifiers($alias_np.\'_\'.$alias), $alias_np.\'_\'.$alias, $this_alias);
}');
		
		$this->addPart('return $arr;');
	}
}


/* End of file GetJoinPart.php */
/* Location: ./lib/Rdm/Builder/Collection */