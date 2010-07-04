<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection extends Rdm_Util_Code_ClassBuilder
{
	/**
	 * The class name, including namespace, to check for.
	 * 
	 * @var string
	 */
	protected $class_check = '';
	
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->class_check = $desc->getCollectionClassName(true);
		$this->setClassName($desc->getCollectionClassName());
		$this->setExtends($desc->getBaseCollectionClassName());
		
		$this->addPart('// Intentionally left empty, this class can be replaced by a user class with the same name');
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		return 'if( ! class_exists(\''.$this->class_check.'\'))
{
	'.self::indentCode(parent::__toString()).'
}';
	}
}


/* End of file Collection.php */
/* Location: ./lib/Rdm/Builder */