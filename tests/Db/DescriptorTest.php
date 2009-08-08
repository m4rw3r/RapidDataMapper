<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../lib/Db.php';

Db::initAutoload();

/**
 * Tests the main Db object.
 */
class Db_DescriptorTest extends PHPUnit_Framework_TestCase
{
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassName()
	{
		$desc = new Db_Descriptor();
		
		$desc->getClass();
	}
	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassName2()
	{
		$desc = new Db_Descriptor();
		
		$desc->getSingular();
	}
	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassName3()
	{
		$desc = new Db_Descriptor();
		
		$desc->getTable();
	}
	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassName4()
	{
		$desc = new Db_Descriptor();
		
		$desc->getFactory();
	}
}


/* End of file Db.php */
/* Location: ./tests/DbTest.php */