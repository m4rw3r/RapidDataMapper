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