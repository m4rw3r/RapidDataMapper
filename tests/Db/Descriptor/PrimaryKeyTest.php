<?php
/*
 * Created by Martin Wernståhl on 2009-11-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Descriptor_PrimaryKey
 */
class Db_Descriptor_PrimaryKeyTest extends PHPUnit_Framework_TestCase
{
	function setUp()
	{
		// constants:
		require_once dirname(__FILE__).'/../../../lib/Db/Descriptor.php';
		
		// classes:
		require_once dirname(__FILE__).'/../../../lib/Db/Descriptor/Column.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Descriptor/PrimaryKey.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception/Descriptor/MissingColumnName.php';
	}
	
	// ------------------------------------------------------------------------
	
	public function testPkType()
	{
		$desc = new Db_Descriptor_PrimaryKey();
		
		$this->assertSame(Db_Descriptor::AUTO_INCREMENT, $desc->getPkType());
	}
	public function testPkType2()
	{
		$desc = new Db_Descriptor_PrimaryKey();
		
		$this->assertSame($desc, $desc->setPkType(Db_Descriptor::MANUAL));
		
		$this->assertSame(Db_Descriptor::MANUAL, $desc->getPkType());
	}
	public function testPkType3()
	{
		$desc = new Db_Descriptor_PrimaryKey();
		
		$desc->setPkType(Db_Descriptor::MANUAL);
		
		$this->assertSame(Db_Descriptor::MANUAL, $desc->getPkType());
		
		$this->assertSame($desc, $desc->setPkType(Db_Descriptor::AUTO_INCREMENT));
		
		$this->assertSame(Db_Descriptor::AUTO_INCREMENT, $desc->getPkType());
	}
	
	// ------------------------------------------------------------------------
	
	public function testDataType()
	{
		$desc = $this->getMock('Db_Descriptor_PrimaryKey', array('getPkType'));
		
		$desc->expects($this->exactly(3))->method('getPkType')->will($this->returnValue(Db_Descriptor::AUTO_INCREMENT));
		
		$this->assertEquals('unsigned int', $desc->getDataType());
		$this->assertSame($desc, $desc->setDataType('integer'));
		$this->assertEquals('integer', $desc->getDataType());
		$this->assertSame($desc, $desc->setDataType('varchar'));
		$this->assertEquals('unsigned int', $desc->getDataType());
	}
	public function testDataType2()
	{
		$desc = $this->getMock('Db_Descriptor_PrimaryKey', array('getPkType'));
		
		$desc->expects($this->once())->method('getPkType')->will($this->returnValue(Db_Descriptor::MANUAL));
		
		$this->assertEquals('unsigned int', $desc->getDataType());
	}
	public function testDataType3()
	{
		$desc = $this->getMock('Db_Descriptor_PrimaryKey', array('getPkType'));
		
		$desc->expects($this->exactly(2))->method('getPkType')->will($this->returnValue(Db_Descriptor::MANUAL));
		
		$this->assertEquals('unsigned int', $desc->getDataType());
		$this->assertSame($desc, $desc->setDataType('integer'));
		$this->assertEquals('integer', $desc->getDataType());
	}
	
	// ------------------------------------------------------------------------
	
	public function testIsInsertable()
	{
		$desc = $this->getMock('Db_Descriptor_PrimaryKey', array('getPkType'));
		
		$desc->expects($this->once())->method('getPkType')->will($this->returnValue(Db_Descriptor::AUTO_INCREMENT));
		
		$this->assertSame(false, $desc->isInsertable());
	}
	public function testIsInsertable2()
	{
		$desc = $this->getMock('Db_Descriptor_PrimaryKey', array('getPkType'));
		
		$desc->expects($this->once())->method('getPkType')->will($this->returnValue(Db_Descriptor::MANUAL));
		
		$this->assertSame(true, $desc->isInsertable());
	}
	
	// ------------------------------------------------------------------------
	
	public function testIsUpdatable()
	{
		$desc = new Db_Descriptor_PrimaryKey();
		
		$this->assertEquals(false, $desc->isUpdatable());
	}
	public function testIsUpdatable2()
	{
		$desc = new Db_Descriptor_PrimaryKey();
		
		$this->assertEquals(false, $desc->isUpdatable());
		$this->assertSame($desc, $desc->setUpdatable(false));
		$this->assertEquals(false, $desc->isUpdatable());
		$this->assertSame($desc, $desc->setUpdatable(true));
		$this->assertEquals(true, $desc->isUpdatable());
	}
}


/* End of file PrimaryKeyTest.php */
/* Location: ./tests/Db/Descriptor */