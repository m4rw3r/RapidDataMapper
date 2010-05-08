<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_HydrateObject_CreateInstance extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->addPart('$e = '.$desc->getFactory().';');
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'create_instance';
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		return '	if(isset(self::$unit_of_work->entities[$id]))
	{
		$e = self::$unit_of_work->entities[$id];
	}
	else
	{
		// Create a new instance
		'.self::indentCode(self::indentCode("\n".implode("\n\n", $this->content))).'
		
		self::$unit_of_work->entities[$id] = $e;
		$refresh = true;
	}';
	}
}


/* End of file CreateInstance.php */
/* Location: ./lib/Rdm/Builder/Collection/HydrateObject */