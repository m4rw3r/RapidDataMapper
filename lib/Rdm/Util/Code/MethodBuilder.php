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
	
	protected $is_static = false;
	
	protected $is_public = true;
	
	protected $param_list;
	
	/**
	 * The PHP doc contents which should go into a PHPdoc before this method.
	 * 
	 * @var string
	 */
	protected $php_doc = '';
	
	
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
	 * Returns the method name of the generated method.
	 * 
	 * @return string
	 */
	public function getMethodName()
	{
		return $this->name;
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

	/**
	 * Returns the parameter list of the generated method.
	 * 
	 * @return string
	 */
	public function getParamList()
	{
		return $this->param_list;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the visibility of the generated method, true = public, false = protected,
	 * default = true;
	 * 
	 * @param  boolean
	 * @return void
	 */
	public function setPublic($value = true)
	{
		$this->is_public = $value;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if the generated method is public, false if it is protected.
	 * 
	 * @return boolean
	 */
	public function isPublic()
	{
		return $this->is_public;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets if the generated method is static or not, default = false.
	 * 
	 * @param  boolean
	 * @return void
	 */
	public function setStatic($value = true)
	{
		$this->is_static = $value;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if the generated method is static, false if it is an instance
	 * method.
	 * 
	 * @return boolean
	 */
	public function isStatic()
	{
		return $this->is_static;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Set the PHPdoc of this generated method, the comment will automatically be
	 * formatted into a PHPdoc comment.
	 * 
	 * @param  string
	 * @return void
	 */
	public function setPhpDoc($string)
	{
		$this->php_doc = "/**\n * ".implode("\n * ", explode("\n", $string))."\n */\n";
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the formatted PHPdoc of the generated class.
	 * 
	 * @return string
	 */
	public function getPhpDoc()
	{
		return $this->php_doc;
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'method_'.$this->name;
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$head = $this->php_doc;
		
		$head .= $this->is_public ? 'public' : 'protected';
		
		$head .= $this->is_static ? ' static' : '';
		
		$head .= " function $this->name($this->param_list)\n{";
		
		$contents = implode("\n\n", $this->content);
		
		return $head . self::indentCode("\n" . $contents) . "\n}";
	}
}

/* End of file MethodBuilder.php */
/* Location: ./lib/Rdm/Util/Code */