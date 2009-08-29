<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../../lib/Db.php';

Db::initAutoload();

/**
 * @covers Db_Descriptor_Column
 */
class Db_Descriptor_ColumnTest extends PHPUnit_Framework_TestCase
{
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Db_Exception_Descriptor_MissingColumnName
	 */
	public function testGetColumn()
	{
		$desc = new Db_Descriptor_Column();
		
		$desc->getColumn();
	}
	public function testGetColumn2()
	{
		$desc = new Db_Descriptor_Column();
		
		$desc->setColumn('foo');
		
		$this->assertEquals('foo', $desc->getColumn());
	}
	/**
	 * @expectedException Db_Exception_Descriptor_MissingColumnName
	 */
	public function testGetProperty()
	{
		$desc = new Db_Descriptor_Column();
		
		$desc->getProperty();
	}
	public function testGetProperty2()
	{
		$desc = new Db_Descriptor_Column();
		
		$desc->setProperty('FooBar');
		
		$this->assertEquals('FooBar', $desc->getProperty());
	}
}


/* End of file Column.php */
/* Location: ./tests/Db/Descriptor */