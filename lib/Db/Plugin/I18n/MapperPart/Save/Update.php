<?php
/*
 * Created by Martin Wernståhl on 2009-08-10.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the update part of the save() method.
 */
class Db_Plugin_I18n_MapperPart_Save_Update extends Db_CodeBuilder_Container
{
	function __construct(Db_Descriptor $descriptor, Db_Plugin_I18n $plugin)
	{
		$db = $descriptor->getConnection();
		
		// HOOK: on_update
		$this->addPart($descriptor->getHookCode('on_update', '$object'));
		
		// assign the data to $data
		$arr = array('//collect data', '$data = array();', '$lang_data = array();');
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
		
		$this->addPart("// just update the data which have been changed\n\$save_data = array_diff_assoc(\$data, \$object->__data);\n\$save_lang_data = array_diff_assoc(\$lang_data, \$object->__data);");
		
		foreach($descriptor->getRelations() as $rel)
		{
			$this->addPart($rel->getSaveUpdateRelationCode('$object'));
		}
		
		// TODO: Add a way to determine if to get all the data instead of only the changed data for translations if a new row needs to be created in the translation table
		
		$this->addPart('
if(empty($save_data) && empty($save_lang_data))
{
	return false;
}

empty($save_data) OR $this->db->update(\''.$descriptor->getTable().'\', $save_data, $object->__id);

if( ! empty($save_lang_data))
{
	// do we have a row in the language table?
	if($this->db->select()->from(\''.$plugin->getLangTable().'\', array())->where(array_merge(array(\''.$plugin->getLangColumn()->getColumn().'\' => $object->'.$plugin->getLangColumn()->getProperty().'), $object->__id))->escape(false)->column(\'1\')->get()->val())
	{
		// existing row
		$this->db->update(\''.$plugin->getLangTable().'\', $save_lang_data, array_merge(array(\''.$plugin->getLangColumn()->getColumn().'\' => $object->'.$plugin->getLangColumn()->getProperty().'), $object->__id));
	}
	else
	{
		// create new row
		$this->db->insert(\''.$plugin->getLangTable().'\', array_merge($save_lang_data, array(\''.$plugin->getLangColumn()->getColumn().'\' => $object->'.$plugin->getLangColumn()->getProperty().'), $object->__id));
	}
}');
		
		$this->addPart('$object->__data = array_merge($data, $lang_data);');
		
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
/* Location: ./lib/Db/Plugin/I18n/MapperPart/Save */