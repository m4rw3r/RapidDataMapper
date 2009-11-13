<?php
/*
 * Created by Martin Wernståhl on 2009-06-12.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class which generates code for a method.
 */
class Db_CodeBuilder_Method extends Db_CodeBuilder_Container
{
	public $name;
	
	protected $param_list;
	
	function __construct($name, $parameter_list = '')
	{
		$this->name = $name;
		$this->param_list = $parameter_list;
	}
	
	public function getName()
	{
		return 'method_'.$this->name;
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$head = "public function $this->name($this->param_list)\n{";
		
		$contents = implode("\n\n", $this->content);
		
		return $head . self::indentCode("\n" . $contents) . "\n}";
	}
}

/* End of file Method.php */
/* Location: ./lib/Db/CodeBuilder */