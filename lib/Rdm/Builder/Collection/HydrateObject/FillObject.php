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
		$this->addPart('$e = '.$desc->getFactory().';');
		
		$shadow_id = array();
		$properties = array();
		foreach($desc->getPrimaryKeys() as $pk)
		{
			$value = '$row[$map[$alias.\'.'.$pk->getProperty().'\']]';
			$shadow_id[] = '\''.$pk->getColumn().'\' => '.$pk->getCastToPhpCode($value);
			$properties[] = $pk->getAssignToObjectCode('$e', '$e->__id[\''.$pk->getColumn().'\']');
		}
		
		$this->addPart('$e->__id = array('.implode(', ', $shadow_id).');');
		$this->addPart(implode("\n", $properties));
		
		$properties = array();
		$shadow_data = array();
		foreach($desc->getColumns() as $c)
		{
			$properties[] = $c->getAssignToObjectCode('$e', $c->getCastToPhpCode('$row[$map[$alias.\'.'.$c->getProperty().'\']]'));
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
		return self::indentCode(self::indentCode("\n".implode("\n\n", $this->content)));
	}
}


/* End of file FillObject.php */
/* Location: ./lib/Rdm/Builder/Collection/HydrateObject */