<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the code responsible for ensuring that an object instance exists in $res[$uid],
 * if an instance does not exist it will create it.
 */
class Db_Mapper_Part_Objectify_NewObj extends Db_CodeBuilder_Container
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->addPart('$obj = '.$descriptor->getFactory().';');
		
		// let the primary key assignments do their part
		// group them together so they are easily spotted
		$arr = array('$obj->__id = array();');
		foreach($descriptor->getPrimaryKeys() as $key)
		{
			$arr[] = $key->getFromDataToObjectCode('$obj', '$row', '$alias');
		}
		$this->addPart(implode("\n", $arr));
		
		// the same goes for the columns
		$arr = array();
		foreach($descriptor->getColumns() as $col)
		{
			$arr[] = $col->getFromDataToObjectCode('$obj', '$row', '$alias');
		}
		$this->addPart(implode("\n", $arr));
		
		// Create the comparable
		$this->addPart('$obj->__data = array();');
		$arr = array();
		foreach(array_merge($descriptor->getColumns(), $descriptor->getPrimaryKeys()) as $col)
		{
			$arr[] = '$obj->__data[\''.$col->getColumn().'\'] = '.$col->getFromObjectCode('$obj').';';
		}
		$this->addPart(implode("\n", $arr));
		
		// assign the object to the proper key
		$this->addPart('$res[$uid] = $obj;');
	}
	
	public function getName()
	{
		return 'new_obj';
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$str = "if( ! isset(\$res[\$uid]))\n{";
		
		$str .= self::indentCode("\n".implode("\n\n", $this->content));
		
		return $str."\n}";
	}
}


/* End of file NewObj.php */
/* Location: ./lib/Db/Mapper/Part/Objectify */