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
		// assign the data to $data
		$arr = array('//collect data', '$data = array();');
		foreach(array_merge($this->descriptor->getColumns(), $this->descriptor->getPrimaryKeys()) as $prop)
		{
			$v = $prop->getFromObjectToDataCode('$object', '$data', true);
			
			// The if( ! empty()) is there to make the code more beautiful
			if( ! empty($v))
			{
				$arr[] = $v;
			}
		}
		$this->addPart(implode("\n", $arr));
		
		$this->addPart("// just update the data which have been changed\n\$save_data = array_diff_assoc(\$data, \$object->__data);");
		
		// TODO: Add relation saving
		
		$this->addPart('if(empty($save_data) OR $this->db->update(\''.$this->descriptor->getTable().'\', $save_data, $object->__id) === false)
{
	return false;
}');
		
		$this->addPart('$object->__data = $data;');
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