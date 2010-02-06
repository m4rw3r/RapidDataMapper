<?php
/*
 * Created by Martin Wernståhl on 2010-01-15.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the code which handles allowDelete() for the object type parameter.
 */
class Db_Mapper_Part_Delete_IsObject extends Db_CodeBuilder_Container
{
	protected $class;
	
	function __construct(Db_Descriptor $descriptor)
	{
		$this->class = $descriptor->getClass();
		
		foreach($descriptor->getRelations() as $rel)
		{
			if($rel->getOnDeleteAction() == Db_Descriptor::SET_NULL)
			{
				// Unlink related rows
				
				$this->addPart($rel->getUnlinkObjectRelationCode('$object'));
			}
		}
		
		$this->addPart('$ret = $this->db->delete(\''.$descriptor->getTable().'\', $object->__id);');
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$str = 'if($object instanceof '.$this->class.")\n{";
		
		$str .= self::indentCode("\n".implode("\n\n", $this->content));
		
		return $str."\n}";
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'object';
	}
}


/* End of file IsObject.php */
/* Location: ./lib/Db/Mapper/Part/AllowDelete */