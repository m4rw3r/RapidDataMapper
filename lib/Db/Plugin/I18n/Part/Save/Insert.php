<?php
/*
 * Created by Martin Wernståhl on 2009-08-10.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the insert part of the save() method.
 */
class Db_Plugin_I18n_Part_Save_Insert extends Db_CodeBuilder_Container
{
	function __construct(Db_Descriptor $descriptor, Db_Plugin_I18n $plugin)
	{
		$db = $descriptor->getConnection();
		
		// HOOK: on_insert
		$this->addPart($descriptor->getHookCode('on_insert', '$object'));
		
		// assign the data to $data
		$arr = array('//collect data', '$data = array();', '$lang_data = array();');
		foreach(array_merge($descriptor->getColumns(), $descriptor->getPrimaryKeys()) as $prop)
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
		foreach(array_merge($descriptor->getColumns(), $descriptor->getPrimaryKeys()) as $prop)
		{
			$this->addPart($prop->getInsertPopulateColumnCode('$data', '$object'));
		}
		
		// HOOK: pre_insert
		$this->addPart($descriptor->getHookCode('pre_insert', '$object', '$data'));
		
		$this->addPart("if(empty(\$data) && empty(\$lang_data))\n{\n\treturn false;\n}");
		
		$this->addPart('if( ! empty($data))
{
	$status = $this->db->insert(\''.$descriptor->getTable().'\', $data);
}
else
{
	// TODO: Does this query work with all databases? (assume auto increment, manual pks aren\'t allowed to come so far if nulled)
	$status = $this->db->query(\'INSERT INTO '.addcslashes($db->protectIdentifiers($descriptor->getTable()), "'").' SET '.addcslashes(array_shift($descriptor->getPrimaryKeys())->getColumn(), "'").' = NULL\');
}');
		
		// on failed save skip saving relations or lang data
		$this->addPart("if( ! \$status)\n{\n\treturn false;\n}");
		
		// assign the database generated values
		foreach(array_merge($descriptor->getColumns(), $descriptor->getPrimaryKeys()) as $prop)
		{
			$this->addPart($prop->getInsertReadColumnCode('$data', '$object'));
		}
		
		// save for future comparison
		$this->addPart("// save the data to be able to only update the modified data\n\$object->__data = array_merge(\$data, \$lang_data);");
		
		// assign the "primary keys"
		foreach($descriptor->getPrimaryKeys() as $prop)
		{
			$this->addPart('isset($object->'.$prop->getProperty().') && $lang_data[\''.$prop->getColumn().'\'] = '.$prop->getCastFromPhpCode($prop->getFromObjectCode('$object')).';');
		}
		
		// save the language strings
		$this->addPart('$this->db->insert(\''.$plugin->getLangTable().'\', $lang_data);');
		
		foreach($descriptor->getRelations() as $rel)
		{
			$this->addPart($rel->getSaveInsertRelationCode('$object'));
		}
		
		// HOOK: post_insert
		$this->addPart($descriptor->getHookCode('post_insert', '$object'));
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
/* Location: ./lib/Db/Plugin/I18n/Part/Save */