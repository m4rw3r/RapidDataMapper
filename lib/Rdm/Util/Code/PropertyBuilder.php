<?php
/*
 * Created by Martin Wernståhl on 2009-06-12.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class which generates code for a property.
 */
class Rdm_Util_Code_PropertyBuilder extends Rdm_Util_Code_Container
{
	protected $name;
	protected $data;
	protected $visibility;
	
	function __construct($name, $data = null, $visibility = 'public')
	{
		$this->name = $name;
		$this->data = $data;
		$this->visibility = $visibility;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the generated property.
	 * 
	 * @return string
	 */
	public function getPropertyName()
	{
		return $this->name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the data which will be converted to PHP and assigned as the
	 * default value of the generated property.
	 * 
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the visibility string for this property ("public", "public static",
	 * "private", "protected" etc.).
	 * 
	 * @return string
	 */
	public function getVisibility()
	{
		return $this->visibility;
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'property_'.$this->name;
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		return "{$this->visibility} \$$this->name".(is_null($this->data) ? ';' : " = ".self::dump_variable($this->data));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Dumps the content of a variable into correct PHP and also nicely indented.
	 * 
	 * Attention!
	 * Cannot handle objects!
	 * 
	 * Usage:
	 * <code>
	 * $str = '$variable = ' . dump_variable($variable);
	 * </code>
	 * 
	 * @param  mixed
	 * @param  int
	 * @return str
	 */
	public static function dump_variable($data, $indent = 0)
	{
		$ind = str_repeat("\t", $indent);
		$str = '';
		
		switch(gettype($data))
		{
			case 'boolean':
				$str .= $data ? 'true' : 'false';
				break;
				
			case 'integer':
			case 'double':
				$str .= $data;
				break;
				
			case 'string':
				$str .= "'". addcslashes($data, '\'\\') . "'";
				break;
				
			case 'array':
				$str .= "array(\n";
				
				$t = array();
				foreach($data as $k => $v)
				{
					$s = '';
					if( ! is_numeric($k))
					{
						$s .= $ind . "\t'".addcslashes($k, '\'\\')."' => ";
					}
					
					$s .= self::dump_variable($v, $indent + 1);
					
					$t[] = $s;
				}
				
				$str .= implode(",\n", $t) . "\n" . $ind . "\t)";
				break;
				
			default:
				$str .= 'NULL';
		}
		
		return $str . ($indent ? '' : ';');
	}
}

/* End of file PropertyBuilder.php */
/* Location: ./lib/Rdm/Util/Code */