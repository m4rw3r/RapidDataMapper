<?php
/*
 * Created by Martin Wernståhl on 2009-08-08.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Uses the Composite pattern to construct code.
 * 
 * An object of this type is usually the root node for a code tree.
 */
abstract class Db_CodeBuilder_Container
{
	/**
	 * Array of nested parts.
	 * 
	 * @var array
	 */
	protected $content = array();
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name / identifier of this CodeContainer.
	 * 
	 * @return string
	 */
	abstract public function getName();
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a part to this container.
	 * 
	 * NOTE:
	 * Strings cannot be replaced as they have no identifier (ie. a getName() method).
	 * 
	 * @param  Db_CodeBuilder_Container|string
	 * @param  string	The path to the destination container (which will contain the object/string)
	 * @param  bool		If to replace an existing code part with same name (located in the destination container)
	 * @return bool		True if the object/string has been added/replaced
	 */
	final public function addPart($part, $path = '', $replace = false)
	{
		if( ! is_string($part) && ! $part instanceof self)
		{
			throw new InvalidArgumentException(is_object($part) ? get_class($part) : gettype($part));
		}
		
		if(empty($part))
		{
			return true;
		}
		
		if(empty($path))
		{
			if($replace && $part instanceof self)
			{
				// return:
				$replaced = false;
				
				// replace the CodeContainer with a new one
				foreach($this->content as $k => $p)
				{
					// matching names
					if($p instanceof self && $part->getName() == $p->getName())
					{
						// replace:
						$this->content[$k] = $part;
						
						$replaced = true;
					}
				}
				
				// sort, so we preserve the original order:
				ksort($this->content);
				
				return $replaced;
			}
			else
			{
				$this->content[] = $part;
				
				return true;
			}
		}
		else
		{
			// get the first dot to separate segments
			$p = ($p = strpos($path, '.')) === false ? strlen($path) : $p;
			
			// get key and then the rest of it
			$key = substr($path, 0, $p);
			$path = substr($path, $p + 1);
			
			// return variable
			$part_added = false;
			
			// find the container to add it to
			foreach($this->content as $container)
			{
				if($container instanceof self)
				{
					if($container->getName() != $key)
					{
						continue;
					}
					
					$r = $container->addPart($part, $path, $replace);
					
					$part_added = ($part_added OR $r);
				}
			}
			
			return $part_added;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Removes a code part from this container.
	 * 
	 * @param  string	The path to the container to remove (includes the container to remove)
	 * @return bool
	 */
	public function removePart($path)
	{
		// get the first dot to separate segments
		$p = ($p = strpos($path, '.')) === false ? strlen($path) : $p;
		
		// get key and then the rest of it
		$key = substr($path, 0, $p);
		$path = substr($path, $p + 1);
		
		// return variable
		$part_removed = false;
		
		foreach($this->content as $k => $part)
		{
			if($part instanceof self)
			{
				if($part->getName() != $key)
				{
					continue;
				}
				
				if( ! empty($path))
				{
					$r = $part->removePart($path);
					
					$part_removed = ($part_removed OR $r);
				}
				else
				{
					unset($this->content[$k]);
					
					$part_removed = true;
				}
			}
		}
		
		return $part_removed;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of the objects contained in this CodeContainer.
	 * 
	 * @return array
	 */
	public function getContent()
	{
		return $this->content;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Renders a list of all the nodes in this container, useful for debug.
	 * 
	 * Format:
	 * <code>
	 * child1
	 * child1.grandchild1
	 * child1.grandchild2
	 * child1.grandchild2.grandgrandchild1
	 * child2
	 * </code>
	 * 
	 * @return array
	 */
	public function generateGraph()
	{
		$arr = array();
		
		foreach($this->content as $c)
		{
			if( ! $c instanceof self)
			{
				continue;
			}
			
			$arr[] = $c->getName();
			
			foreach($c->generateGraph() as $g)
			{
				$arr[] = $c->getName().'.'.$g;
			}
		}
		
		return $arr;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Renders this code container.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return implode("\n\n", $this->content);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Indents the code one tab.
	 * 
	 * @param  string
	 * @return string
	 */
	public static function indentCode($code)
	{
		$str = '';
		
		// get the comments
		while(preg_match('@^([\w\W]*?)(//[^\n]*\n|/\*[\w\W]*\*/)([\w\W]*)$@', $code, $match))
		{
			list(,$pre, $comment, $post) = $match;
			
			// replace quotes in the comments
			$str .= $pre.str_replace(array("'", '"'), array("[!<SMALLQUOTE>!]", '[!<LARGEQUOTE>!]'), $comment);
			$code = $post;
		}
		
		// also add the last, unmatched, part
		$code = $str.$code;
		$str = '';
		
		// skip all the strings
		while(preg_match('@^([\w\W]*?)((\'|")[\w\W]*?(?<!\\\\)\\3)([\w\W]*)$@', $code, $match)) 
		{
			list(, $pre, $no_indent, ,$post) = $match;
			
			$str .= str_replace("\n", "\n\t", $pre).$no_indent;
			$code = $post;
		}
		
		$code = $str.str_replace("\n", "\n\t", $code);
		
		return str_replace(array("[!<SMALLQUOTE>!]", '[!<LARGEQUOTE>!]'), array("'", '"'), $code);
	}
}


/* End of file Container.php */
/* Location: ./lib/Db/CodeBuilder */