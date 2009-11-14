<?php
/*
 * Created by Martin Wernståhl on 2009-11-13.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class which generates code for a class.
 */
class Db_CodeBuilder_Class extends Db_CodeBuilder_Container
{
	public $name = '';
	
	public $extends = '';
	
	public $implements = '';
	
	public function getName()
	{
		return 'class_'.$this->name;
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$head = "class {$this->name}";
		
		if( ! empty($this->extends))
		{
			$head .= " extends {$this->extends}";
		}
		
		if( ! empty($this->implements))
		{
			$head .= " implements {$this->implements}";
		}
		
		$head .= "\n{";
		
		$contents = implode("\n\n", $this->content);
		
		return $head . self::indentCode("\n" . $contents) . "\n}";
	}
}

/* End of file Class.php */
/* Location: ./lib/Db/CodeBuilder */