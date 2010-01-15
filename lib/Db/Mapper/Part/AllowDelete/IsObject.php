<?php
/*
 * Created by Martin Wernståhl on 2010-01-15.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the code which handles allowDelete() for the object type parameter.
 */
class Db_Mapper_Part_AllowDelete_IsObject extends Db_CodeBuilder_Container
{
	protected $class;
	
	function __construct(Db_Descriptor $descriptor)
	{
		$this->class = $descriptor->getClass();
		
		$str = array();
		
		foreach($descriptor->getRelations() as $rel)
		{
			if($rel->getOnDeleteAction() != Db_Descriptor::RESTRICT)
			{
				continue;
			}
			
			$str[] = 'empty($object->'.$rel->getProperty().')';
		}
		
		// Assemble all into a nice happy one-liner
		$this->addPart('if( ! ('.implode(' && ', $str).'))
{
	// Related data loaded, no need to check the db
	return false;
}');
		
		$this->addPart(new Db_Mapper_Part_AllowDelete_CountRowsObject($descriptor));
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