<?php
/*
 * Created by Martin Wernståhl on 2010-11-24.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * TODO: Implement more annotation names.
 */
class Rdm_Util_DescriptorLoader_Annotation_Factory implements Rdm_Util_Annotation_FactoryInterface
{
	protected $descriptor = null;
	
	protected $reflector = null;
	
	// ------------------------------------------------------------------------

	/**
	 * Uses the annotation data to populate the internal descriptor object instead
	 * of creating annotation objects.
	 * 
	 * @param  string
	 * @param  array|null
	 * @return boolean
	 */
	public function createObject($class, $params)
	{
		$class = strtolower($class);
		
		switch($class)
		{
			case 'primarykey':
				
				if( ! $this->reflector instanceof ReflectionProperty)
				{
					throw new Exception('The PrimaryKey annotation can only be used on a property.');
				}
				
				$c = $this->descriptor->newPrimaryKey($this->reflector->getName());
				
				// TODO: Parameters
				$this->descriptor->add($c);
				
				break;
				
			case 'column':
				
				if( ! $this->reflector instanceof ReflectionProperty)
				{
					throw new Exception('The Column annotation can only be used on a property.');
				}
				
				$c = $this->descriptor->newColumn($this->reflector->getName());
				
				if( ! empty($params['type']))
				{
					switch(strtolower($params['type']))
					{
						case 'int':
							
							$c->setDataType(Rdm_Descriptor::INT);
							
							break;
					}
				}
				
				// TODO: More parameters
				$this->descriptor->add($c);
				
				break;
				
			case 'relation':
				
				if( ! $this->reflector instanceof ReflectionProperty)
				{
					throw new Exception('The Relation annotation can only be used on a property.');
				}
				
				$r = $this->descriptor->newRelation($this->reflector->getName());
				
				// TODO: Parameters
				$this->descriptor->add($r);
				
				break;
				
			default:
				
				return false;
		}
		
		return true;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function setDescriptor(Rdm_Descriptor $desc)
	{
		$this->descriptor = $desc;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function setClass(ReflectionClass $c)
	{
		$this->setCurrent($c);
		$this->descriptor->setClass($c->getName());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function setCurrent(Reflector $r)
	{
		$this->reflector = $r;
	}
}


/* End of file Factory.php */
/* Location: ./lib/Rdm/Util/DescriptorLoader/Annotation */