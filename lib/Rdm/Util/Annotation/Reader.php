<?php
/*
 * Created by Martin Wernståhl on 2010-11-23.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Utility class which reads and parses annotations for supplied ReflectionClass,
 * ReflectionMethod, ReflectionProperty and ReflectionFunction objects.
 * 
 * Annotation syntax:
 * <code>
 * #[AnnotationName]
 * function foo()...  // Or class, property or method
 * 
 * #[AnnotationName(parameter1=value1,parameter2=value2...)]
 * class Foo... // Or method, property or function
 * 
 * #[AnnotationName(a=[NestedAnnotationName(param1=2, param2=v2...)],b=c...)]
 * public $test... // Or class, method or function
 * </code>
 * 
 * Usage:
 * <code>
 * $factory = new Rdm_Util_Annotation_Factory();
 * $reader  = new Rdm_Util_Annotation_Reader($factory);
 * 
 * var_dump($reader->readAnnotations(new ReflectionClass('Foo')));
 * </code>
 * 
 * TODO: Cache generated results, so we don't have to reparse the stored data again
 */
class Rdm_Util_Annotation_Reader
{
	const T_CLASS = 'CLASS';
	const T_KEY = 'KEY';
	const T_BEGIN_PARAMS = 'BEGIN_PARAMS';
	const T_END_PARAMS = 'END_PARAMETERS';
	const T_END_CLASS = 'END_CLASS';
	const T_COMMA = 'COMMA';
	const T_DATA = 'DATA';
	
	/**
	 * The object creating the annotation objects.
	 * 
	 * @var Rdm_Util_Annotation_Factory
	 */
	protected $factory = null;
	
	/**
	 * Contains the line number which is currently being parsed.
	 * 
	 * @var int
	 */
	protected $current_line = 0;
	
	/**
	 * Contains the file name which is currently being parsed.
	 * 
	 * @var string
	 */
	protected $current_file = "";
	
	/**
	 * Data which already has been scanned.
	 * 
	 * @var array(string => array(string))
	 */
	protected $scanned_data = array();
	
	/**
	 * Cache for the already generated annotations.
	 * 
	 * @var array(string => mixed)
	 */
	protected $annotation_cache = array();
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __construct(Rdm_Util_Annotation_FactoryInterface $factory)
	{
		$this->factory = $factory;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Reads the annotations for the supplied Reflector.
	 * 
	 * @param  ReflectionClass|ReflectionProperty|ReflectionMethod
	 * @return array(Rdm_Util_Annotation_AnnotationInterface)
	 */
	public function readAnnotations(Reflector $ref)
	{
		// Create the key for the appropriate type, and perform type validation
		$data_key = $this->getDataKey($ref);
		
		// Reuse already generated annotations
		if(array_key_exists($data_key, $this->annotation_cache))
		{
			return $this->annotation_cache[$data_key];
		}
		
		// Get file name
		$file = $this->getCodeFile($ref);
		
		// Have we scanned the file earlier?
		if( ! array_key_exists($file, $this->scanned_data))
		{
			$this->scanFile($file);
		}
		
		if(empty($this->scanned_data[$file][$data_key]))
		{
			// Cache
			$this->annotation_cache[$data_key] = array();
			
			return array();
		}
		else
		{
			// Parse the annotations we found
			$annot = array();
			$this->current_file = $file;
			
			// Scan and parse
			foreach($this->scanned_data[$file][$data_key] as $line)
			{
				// Only scan lines which match the annotation pattern
				if(preg_match('/#\s*(\[\s*\w[^\n]*\])\s*$/', $line[0], $matches))
				{
					$this->current_line = $line[1];
					$tokens = $this->tokenizeAnnotationString($matches[1]);
					
					$annot[] = $this->parseTokens($tokens);
				}
			}
			
			// Reset
			$this->current_line = 0;
			$this->current_file = '';
			
			// Cache
			$this->annotation_cache[$data_key] = $annot;
			
			return $annot;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Scans a file for annotation data and stores it internally.
	 * 
	 * @param  string
	 * @return void
	 */
	public function scanFile($file)
	{
		// Get code and init
		$code = file_get_contents($file);
		$state = 0; // 0 = scanning, 1 = class found, 2 = function/method found
		$data = array();
		$class = null;
		$indent = 0;
		$class_indent = -1;
		
		// Let PHP tokenize the stuff so we have an accurate representation
		foreach(token_get_all($code) as $tok)
		{
			// Normalize tokens
			$tok = is_array($tok) ? $tok : array(0, $tok);
			
			switch($tok[0])
			{
				// Ignore these while parsing annotations
				case T_PUBLIC:
				case T_PROTECTED:
				case T_PRIVATE:
				case T_FINAL:
				case T_VAR:
				case T_ABSTRACT:
				case T_WHITESPACE:
				case T_DOC_COMMENT:
				case T_BAD_CHARACTER:
					
					continue;
					
				// We've found a class, next T_STRING is the class name
				case T_CLASS:
				case T_INTERFACE:
					
					$state = 1;
					
					break;
					
				// We've found a variable, save annotations if it is part of a class
				case T_VARIABLE:
					
					if( ! empty($class))
					{
						$data[$class.$tok[1]] = $annot;
					}
					
					$annot = array();
					
					break;
					
				// We've got a function, next T_STRING is the function name
				case T_FUNCTION:
					
					$state = 2;
					
					break;
					
				case T_STRING:
					
					switch($state)
					{
						// Class, save class name for other classes
						case 1:
							
							$data[$tok[1]] = $annot;
							$class = $tok[1];
							$class_indent = $indent;
							
							break;
							
						// Annoted function
						case 2:
							
							if( ! empty($class))
							{
								$data[$class.'>'.$tok[1]] = $annot;
							}
							else
							{
								$data['>'.$tok[1]] = $annot;
							}
							
							break;
					}
					
					$annot = array();
					$state = 0;
					
					break;
					
				// Comments can be annotations
				case T_COMMENT:
					
					// Include line numbers
					$annot[] = array($tok[1], $tok[2]);
					
					break;
					
				// Reset annotation buffer on others
				default:
					
					$annot = array();
			}
			
			// Keep track of indent
			if($tok[1] == '{')
			{
				$indent++;
			}
			elseif($tok[1] == '}')
			{
				$indent--;
				
				// Remove class if we have exited it
				if($indent <= $class_indent)
				{
					$class = null;
				}
			}
		}
		
		$this->scanned_data[$file] = $data;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Tokenizes the annotation and checks for some syntax errors.
	 * 
	 * @param  string
	 * @return array(int, string)
	 */
	protected function tokenizeAnnotationString($string)
	{
		$class = 0;
		$param = 0;
		$tokens = array();
		
		while(preg_match('/^\s*(?:\[\s*(\w*)|(\()|([\w\'"\-]+)\s*=|(\))|(\])|(,)|(\w+))([\w\W]*)/', $string, $matches))
		{
			list(, $class_name, $begin_params, $key_val_assignment, $end_params, $end_class, $comma, $data, $string) = $matches;
			
			if( ! empty($class_name))
			{
				$class++;
				$tokens[] = array(self::T_CLASS, $class_name);
			}
			elseif( ! empty($begin_params))
			{
				$param++;
				$tokens[] = array(self::T_BEGIN_PARAMS);
			}
			elseif( ! empty($key_val_assignment))
			{
				$tokens[] = array(self::T_KEY, $key_val_assignment);
			}
			elseif( ! empty($end_params))
			{
				$param--;
				
				if($param < 0)
				{
					throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Syntax error, unexpected \')\' at line %d in file "%s".', $this->current_line, $this->current_file));
				}
				
				$tokens[] = array(self::T_END_PARAMS);
			}
			elseif( ! empty($end_class))
			{
				$class--;
				
				if($class < 0)
				{
					throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Syntax error, unexpected \']\' at line %d in file "%s".', $this->current_line, $this->current_file));
				}
				
				$tokens[] = array(self::T_END_CLASS);
			}
			elseif( ! empty($comma))
			{
				$tokens[] = array(self::T_COMMA);
			}
			elseif( ! empty($data))
			{
				$tokens[] = array(self::T_DATA, $data);
			}
		}
		
		if($param > 0)
		{
			throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Syntax error, expecting \')\' at line %d in file "%s".', $this->current_line, $this->current_file));
		}
		
		if($class > 0)
		{
			throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Syntax error, expecting \']\' at line %d in file "%s".', $this->current_line, $this->current_file));
		}
		
		if( ! preg_match('/^\s*$/', $string))
		{
			throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Syntax error on "%s" at line %d in file "%s".', $string, $this->current_line, $this->current_file));
		}
		
		return $tokens;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Parses an array of tokens into the annotations, calls the annotation factory
	 * to create the annotations
	 * 
	 * @param  array(int, string)
	 * @return mixed
	 */
	protected function parseTokens(&$token_stream)
	{
		$ret = null;
		
		while( ! empty($token_stream))
		{
			$token = array_shift($token_stream);
			
			switch($token[0])
			{
				case self::T_CLASS:
					
					if($ret !== null)
					{
						throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Unexpected annotation class at line %d in file "%s"', $this->current_line, $this->current_file));
					}
					
					return $this->factory->createObject($token[1], $this->parseTokens($token_stream));
					
				case self::T_BEGIN_PARAMS:
					
					if($ret !== null)
					{
						throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Unexpected parameter list at line %d in file "%s".', $this->current_line, $this->current_file));
					}
					
					$ret = array();
					
					break;
				case self::T_KEY:
					
					if( ! is_array($ret))
					{
						throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Unexpected assignment to "'.$token[1].'" at line %d in file "%s".', $this->current_line, $this->current_file));
					}
					
					$ret[$token[1]] = $this->parseTokens($token_stream);
					
					break;
				case self::T_END_PARAMS:
					
					if( ! is_array($ret))
					{
						throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Unexpected \')\' at line %d in file "%s".', $this->current_line, $this->current_file));
					}
					
					return $ret;
				
				case self::T_DATA:
					
					return $this->parseData($token[1]);
					
				case self::T_END_CLASS:
					
					return $ret;
					
				case self::T_COMMA:
					
					// Intentionally left empty
			}
		}
		
		return $ret;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Parses a data node into appropriate type.
	 * 
	 * @param  string
	 * @return mixed
	 */
	public function parseData($value)
	{
		// TODO: Code
		return $value;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Validates the supplied Reflector to prevent types which cannot have annotations,
	 * returns the data key for those passing the validation, otherwise it throws an exception.
	 * 
	 * @param  Reflector
	 * @return string
	 */
	protected function getDataKey(Reflector $ref)
	{
		if($ref instanceof ReflectionClass)
		{
			return $ref->getName();
		}
		elseif($ref instanceof ReflectionFunction)
		{
			return '>'.$ref->getName();
		}
		elseif($ref instanceof ReflectionMethod)
		{
			return $ref->getDeclaringClass()->getName().'>'.$ref->getName();
		}
		elseif($ref instanceof ReflectionProperty)
		{
			return $ref->getDeclaringClass()->getName().'$'.$ref->getName();
		}
		else
		{
			throw new Rdm_Util_Annotation_Exception(sprintf('Annotation: Unsupported reflector of type "%s"', get_class($ref)));
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the file name containing the file to be parsed.
	 * 
	 * @param  Reflector
	 * @return string
	 */
	public function getCodeFile(Reflector $ref)
	{
		if($ref instanceof ReflectionClass OR $ref instanceof ReflectionFunction)
		{
			return $ref->getFileName();
		}
		else
		{
			return $ref->getDeclaringClass()->getFileName();
		}
	}
}


/* End of file Reader.php */
/* Location: ./lib/Rdm/Util/Annotation */