<?php
/*
 * Created by Martin Wernståhl on 2009-08-10.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the insert part of the save() method.
 */
class Db_Mapper_Part_Save_Insert extends Db_Mapper_CodeContainer
{
	protected $descriptor;
	
	function __construct(Db_Descriptor $desc)
	{
		$this->descriptor = $desc;
		
		$this->addContent();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Populates this object
	 * 
	 * @return void
	 */
	public function addContent()
	{
		// HOOK: on_insert
		$this->addPart($this->descriptor->getHookCode('on_insert', '$object'));
		
		// assign the data to $data
		$arr = array('//collect data', '$data = array();');
		foreach(array_merge($this->descriptor->getColumns(), $this->descriptor->getPrimaryKeys()) as $prop)
		{
			$v = $prop->getFromObjectToDataCode('$object', '$data');
			
			// The if( ! empty()) is there to make the code more beautiful
			if( ! empty($v))
			{
				$arr[] = $v;
			}
		}
		$this->addPart(implode("\n", $arr));
		
		// assign the generated values and add the preprocessing and/or validation
		foreach(array_merge($this->descriptor->getColumns(), $this->descriptor->getPrimaryKeys()) as $prop)
		{
			$this->addPart($prop->getInsertPopulateColumnCode('$data', '$object'));
		}
		
		// HOOK: pre_insert
		$this->addPart($this->descriptor->getHookCode('pre_insert', '$object', '$data'));
		
		$this->addPart("if(empty(\$data))\n{\n\treturn false;\n}");
		
		$this->addPart('$status = $this->db->insert(\''.$this->descriptor->getTable().'\', $data);');
		
		// on failed save skip saving relations
		$this->addPart("if( ! \$status)\n{\n\treturn false;\n}");
		
		// assign the database generated values
		foreach(array_merge($this->descriptor->getColumns(), $this->descriptor->getPrimaryKeys()) as $prop)
		{
			$this->addPart($prop->getInsertReadColumnCode('$data', '$object'));
		}
		
		foreach($this->descriptor->getRelations() as $rel)
		{
			$this->addPart($rel->getSaveInsertRelationCode('$object'));
		}
		
		// save for future comparison
		$this->addPart("// save the data to be able to only update the modified data\n\$object->__data = \$data;");
		
		// HOOK: post_insert
		$this->addPart($this->descriptor->getHookCode('post_insert', '$object'));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'insert';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * The insert part is wrapped in an if conditional, which checks if the object has been saved.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$str = "if(empty(\$object->__id))\n{";
		
		$str .= self::indentCode("\n".implode("\n\n", $this->content));
		
		return $str."\n}";
	}
}


/* End of file Inert.php */
/* Location: ./lib/Db/Mapper/Part/Save */