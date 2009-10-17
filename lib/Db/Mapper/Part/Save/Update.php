<?php
/*
 * Created by Martin Wernståhl on 2009-08-10.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the update part of the save() method.
 */
class Db_Mapper_Part_Save_Update extends Db_Mapper_CodeContainer
{
	function __construct(Db_Descriptor $descriptor)
	{
		// HOOK: on_update
		$this->addPart($descriptor->getHookCode('on_update', '$object'));
		
		// assign the data to $data
		$arr = array('//collect data', '$data = array();');
		foreach(array_merge($descriptor->getColumns(), $descriptor->getPrimaryKeys()) as $prop)
		{
			$v = $prop->getFromObjectToDataCode('$object', '$data', true);
			
			// The if( ! empty()) is there to make the code more beautiful
			if( ! empty($v))
			{
				$arr[] = $v;
			}
		}
		$this->addPart(implode("\n", $arr));
		
		// HOOK: pre_update
		$this->addPart($descriptor->getHookCode('pre_update', '$object', '$data'));
		
		$this->addPart("// just update the data which have been changed\n\$save_data = array_diff_assoc(\$data, \$object->__data);");
		
		foreach($descriptor->getRelations() as $rel)
		{
			$this->addPart($rel->getSaveUpdateRelationCode('$object'));
		}
		
		$this->addPart('if(empty($save_data) OR $this->db->update(\''.$descriptor->getTable().'\', $save_data, $object->__id) === false)
{
	return false;
}');
		
		$this->addPart('$object->__data = $data;');
		
		// HOOK: post_update
		$this->addPart($descriptor->getHookCode('post_update', '$object'));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'update';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * The insert part is wrapped in an else, which is run if the object has been saved previously.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$str = "else\n{";
		
		$str .= self::indentCode("\n".implode("\n\n", $this->content));
		
		return $str."\n}";
	}
}


/* End of file Inert.php */
/* Location: ./lib/Db/Mapper/Part/Save */