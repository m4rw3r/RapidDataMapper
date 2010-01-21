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
	/**
	 * Path to the directory for the XML files.
	 * 
	 * @var string
	 */
	protected $base_path;
	
	/**
	 * Descriptor which is being populated.
	 * 
	 * @var Db_Descriptor
	 */
	protected $desc;
	
	/**
	 * Root node of the XML document.
	 * 
	 * @var SimpleXMLElement
	 */
	protected $doc;
	
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
		
		if($this->doc->getName() != 'mapper')
		{
			throw new Db_Exception('An XML file describing a mapper must use the root tag <mapper>.');
		}
		
		if(isset($this->doc['mapperclass']))
		{
			$class = $this->doc['mapperclass'];
		}
		else
		{
			$class = 'Db_Descriptor';
		}
		
		$this->desc = new $class();
		
		$this->parseSimpleXMLElements($this->doc);
		
		return $this->desc;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Goes through all the children of the 
	 * 
	 * @return 
	 */
	public function parseSimpleXMLElements(SimpleXMLElement $node)
	{
		foreach($this->doc->children() as $node)
		{
			$method = 'basetag'.ucfirst(strtolower($node->getName()));
			
			if(method_exists($this, $method))
			{
				$this->$method($node);
			}
		}
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
				throw new Db_Exception('A primary_keys tag can only contain primary_key tags, <'.$n->getName().'> tag encountered in file "'.$this->file.'".');
			}
			
			$this->basetagPrimary_key($n);
		}
	}
	public function tagColumn($node)
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
				throw new Db_Exception('A columns tag can only contain column tags, <'.$n->getName().'> tag encountered in file "'.$this->file.'".');
			}
			
			$this->tagColumn($n);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __call($method, $params = array())
	{
		throw new Exception('Unexpected tag <'.$method.'>.');
	}
}


/* End of file XML.php */
/* Location: ./lib/Db/DescriptorLoader */