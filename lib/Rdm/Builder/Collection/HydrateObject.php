<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_HydrateObject extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('hydrateObject');
		$this->setParamList('&$row, &$result, &$map');
		
		$pks = array();
		foreach($desc->getPrimaryKeys() as $pk)
		{
			$pks[] = '$row[$map[$this->table_alias.\'.'.$pk->getProperty().'\']]';
		}
		
		$this->addPart('$id = '.implode('.', $pks).';');
		
		$this->addPart('if(empty($id))
{
	return false;
}');
		
		$this->addPart(
'if( ! isset($result[$id]))
{
	if(isset(self::$unit_of_work->entities[$id]))
	{
		$result[$id] = $e = self::$unit_of_work->entities[$id];
	}
	else
	{
		// Create a new instance');
		
		$this->addPart(new Rdm_Builder_Collection_HydrateObject_FillObject($desc));
		
		$this->addPart('		$result[$id] = self::$unit_of_work->entities[$id] = $e;
	}
}
else
{
	$e = $result[$id];
}');
		
		$this->addPart(new Rdm_Builder_Collection_HydrateObject_GotoRelated($desc));
	}
}


/* End of file HydrateObject.php */
/* Location: ./lib/Rdm/Builder/Collection */