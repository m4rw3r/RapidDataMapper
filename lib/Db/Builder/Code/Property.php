<?php
/*
 * Created by Martin Wernståhl on 2009-06-12.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class which generates code for a property.
 */
class Db_Mapper_Code_Property extends Db_Mapper_CodeContainer
{
	
	function __construct($name, $data = null)
	{
		$this->name = $name;
		$this->data = $data;
	}
	
	public function getName()
	{
		return 'property_'.$this->name;
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		return "public \$$this->name".(is_null($this->data) ? ';' : " = ".self::dump_variable($this->data));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Dumps the content of a variable into correct PHP.
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

/* End of file property.php */
/* Location: ./lib/code */