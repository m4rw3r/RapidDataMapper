<?php
/*
 * Created by Martin Wernståhl on 2010-01-21.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Db_DescriptorLoader_XML
{
	protected $path;
	
	function __construct($path = '.')
	{
		$this->base_path = $path;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function load($class)
	{
		$this->file = $this->base_path.DIRECTORY_SEPARATOR.$class.'.xml';
		
		$this->doc = simplexml_load_file($this->file);
		
		$this->desc = new Db_Descriptor();
		
		foreach($this->doc->children() as $node)
		{
			$method = 'basetag'.ucfirst(strtolower($node->getName()));
			
			if(method_exists($this, $method))
			{
				$this->$method($node);
			}
		}
		
		return $this->desc;
	}
	
	public function basetagClass($node)
	{
		$this->desc->setClass((String) $node);
	}
	
	public function basetagTable($node)
	{
		$this->desc->setTable((String) $node);
	}
	
	public function basetagSingular($node)
	{
		$this->desc->setSingular((String) $node);
	}
	
	public function basetagFactory($node)
	{
		$this->desc->setFactory((String) $node);
	}
	
	public function basetagPrimary_key($node)
	{
		$k = $this->desc->newPrimaryKey((String) $node);
		
		// TODO: Let the user have attributes on the tag
		
		$this->desc->add($k);
	}
	public function basetagPrimary_keys($node)
	{
		foreach($node->children() as $n)
		{
			// Only allow primary_key tags as direct descendants
			if($n->getName() != 'primary_key')
			{
				throw new Db_Exception('A primary_keys tag can only contain primary_key tags, '.$n->getName().' tag encountered in file "'.$this->file.'".');
			}
			
			$this->basetagPrimary_key($n);
		}
	}
	public function basetagColumn($node)
	{
		$k = $this->desc->newColumn((String) $node);
		
		// TODO: Let the user have attributes on the tag
		
		$this->desc->add($k);
	}
	public function basetagColumns($node)
	{
		foreach($node->children() as $n)
		{
			// Only allow column tags
			if($n->getName() != 'column')
			{
				throw new Db_Exception('A columns tag can only contain column tags, '.$n->getName().' tag encountered in file "'.$this->file.'".');
			}
			
			$this->basetagColumn($n);
		}
	}
}


/* End of file XML.php */
/* Location: ./lib/Db/DescriptorLoader */