<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_HydrateObject_FillObject extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$shadow_id = array();
		$properties = array();
		foreach($desc->getPrimaryKeys() as $pk)
		{
			$value = '$row[$map[$this->table_alias.\'.'.$pk->getProperty().'\']]';
			$shadow_id[] = '\''.$pk->getColumn().'\' => '.$pk->getDataTypeObject()->getCastToPhpCode($value);
			$properties[] = $pk->getAssignToObjectCode('$e', '$e->__id[\''.$pk->getColumn().'\']');
		}
		
		$this->addPart('$e->__id = array('.implode(', ', $shadow_id).');');
		$this->addPart(implode("\n", $properties));
		
		$properties = array();
		$shadow_data = array();
		foreach($desc->getColumns() as $c)
		{
			$properties[] = $c->getAssignToObjectCode('$e', $c->getDataTypeObject()->getCastToPhpCode('$row[$map[$this->table_alias.\'.'.$c->getProperty().'\']]'));
			$shadow_data[] = '\''.$c->getColumn().'\' => '.$c->getFetchFromObjectCode('$e');
		}
		
		$this->addPart(implode("\n", $properties));
		$this->addPart('$e->__data = array('.implode(', ', $shadow_data).');');
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'fill_object';
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		return "\tif(\$refresh)\n\t{".self::indentCode(self::indentCode("\n".implode("\n\n", $this->content)))."\n\t}";
	}
}


/* End of file FillObject.php */
/* Location: ./lib/Rdm/Builder/Collection/HydrateObject */