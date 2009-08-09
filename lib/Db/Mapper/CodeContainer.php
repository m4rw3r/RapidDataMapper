<?php
/*
 * Created by Martin Wernståhl on 2009-08-08.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Uses the Composite pattern to construct code.
 */
abstract class Db_Mapper_CodeContainer
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
	 * @param  Db_Mapper_CodeContainer
	 * @param  string	The path to the destination container
	 * @param  bool
	 * @return bool
	 */
	final public function addPart($part, $path = '', $replace = false)
	{
		if( ! is_string($part) && ! $part instanceof Db_Mapper_CodeContainer)
		{
			throw new InvalidArgumentException(is_object($part) ? get_class($part) : gettype($part));
		}
		
		if(empty($path))
		{
			if($replace && $part instanceof Db_Mapper_CodeContainer)
			{
				// replace the CodeContainer with a new one
				foreach($this->content as $k => $p)
				{
					// matching names
					if($part->getName() == $p->getName())
					{
						unset($this->content[$k]);
					}
				}
			}
			
			$this->content[] = $part;
			
			return true;
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
				if($container instanceof Db_Mapper_CodeContainer)
				{
					if($container->getName() != $key)
					{
						continue;
					}
					
					$part_added = $part_added OR $container->addPart($part, $path);
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
			if($part instanceof Db_Mapper_CodeContainer)
			{
				if($part->getName() != $key)
				{
					continue;
				}
				
				if( ! empty($path))
				{
					$part_removed = $part_removed OR $part->removePart($path);
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
			if( ! $c instanceof Db_Mapper_CodeContainer)
			{
				continue;
			}
			
			$arr[] = $c->getName();
			
			foreach($c->generateGraph() as $g)
			{
				$arr[] = $c->getName().'.'.$p;
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


/* End of file CodeContainer.php */
/* Location: ./lib/Db/Mapper */