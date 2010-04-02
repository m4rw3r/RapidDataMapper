<?php
/*
 * Created by Martin Wernståhl on 2009-06-12.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class which generates code for a method.
 */
class Rdm_Util_Code_MethodBuilder extends Rdm_Util_Code_Container
{
	public $name;
	
	protected $param_list;
	
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the name of the generated method.
	 * 
	 * @param  string
	 * @return void
	 */
	public function setMethodName($name)
	{
		$this->name = $name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the parameter list of the generated method.
	 * 
	 * @param  string
	 * @return void
	 */
	public function setParamList($parameter_list)
	{
		$this->param_list = $parameter_list;
	}
	
	// ------------------------------------------------------------------------
	
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

/* End of file MethodBuilder.php */
/* Location: ./lib/Rdm/Util/Code */