<?php
/*
 * Created by Martin Wernståhl on 2009-11-13.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class which generates code for a class.
 */
class Rdm_Util_Code_ClassBuilder extends Rdm_Util_Code_Container
{
	public $name = '';
	
	public $extends = '';
	
	public $implements = '';
	
	/**
	 * The PHP doc contents which should go into a PHPdoc before this method.
	 * 
	 * @var string
	 */
	public $php_doc = '';
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the name of the generated class.
	 * 
	 * @param  string
	 * @return void
	 */
	public function setClassName($name)
	{
		$this->name = $name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the class name of the generated class.
	 * 
	 * @return string
	 */
	public function getClassName()
	{
		return $this->name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets a class name which the generated class shall extend.
	 * 
	 * @param  string
	 * @return void
	 */
	public function setExtends($extends)
	{
		$this->extends = $extends;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the class name of the class the generated class extends.
	 * 
	 * @return string
	 */
	public function getExtends()
	{
		return $this->extends;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets a list of interfaces which the generated class shall implement.
	 * 
	 * @param  string
	 * @return void
	 */
	public function setImplements($implements)
	{
		$this->implements = $implements;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the list of interfaces (separated by ", ") which the generated
	 * class will implement.
	 * 
	 * @return string
	 */
	public function getImplements()
	{
		return $this->implements;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Set the PHPdoc of this generated class, the comment will automatically be
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
		return 'class_'.$this->name;
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$head = $this->php_doc."class {$this->name}";
		
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

/* End of file ClassBuilder.php */
/* Location: ./lib/Rdm/Util/Code */