<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Descriptor_Column
 */
class Db_Descriptor_ColumnTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../../lib/Db/Descriptor/Column.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception/Descriptor/MissingColumnName.php';
	}
	
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
		
		$this->assertSame($desc, $desc->setColumn('foo'));
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
		
		$this->assertSame($desc, $desc->setProperty('FooBar'));
		$this->assertEquals('FooBar', $desc->getProperty());
	}
	
	// ------------------------------------------------------------------------
	
	public function testDataType()
	{
		$desc = new Db_Descriptor_Column();
		
		$this->assertEquals('varchar', $desc->getDataType());
	}
	public function testDataType2()
	{
		$desc = new Db_Descriptor_Column();
		
		$this->assertSame($desc, $desc->setDataType('integer'));
		$this->assertEquals('integer', $desc->getDataType());
	}
	
	// ------------------------------------------------------------------------
	
	public function testDataLength()
	{
		$desc = new Db_Descriptor_Column();
		
		$this->assertEquals(255, $desc->getDataLength());
	}
	public function testDataLength2()
	{
		$desc = new Db_Descriptor_Column();
		
		$this->assertSame($desc, $desc->setDataLength(45));
		$this->assertEquals(45, $desc->getDataLength());
		$this->assertSame($desc, $desc->setDataLength(47));
		$this->assertEquals(47, $desc->getDataLength());
	}
	
	// ------------------------------------------------------------------------
	
	public function testIsInsertable()
	{
		$desc = new Db_Descriptor_Column();
		
		$this->assertEquals(true, $desc->isInsertable());
	}
	public function testIsInsertable2()
	{
		$desc = new Db_Descriptor_Column();
		
		$this->assertSame($desc, $desc->setInsertable(false));
		$this->assertEquals(false, $desc->isInsertable());
		$this->assertSame($desc, $desc->setInsertable(true));
		$this->assertEquals(true, $desc->isInsertable());
	}
	
	// ------------------------------------------------------------------------
	
	public function testIsUpdatable()
	{
		$desc = new Db_Descriptor_Column();
		
		$this->assertEquals(true, $desc->isUpdatable());
	}
	public function testIsUpdatable2()
	{
		$desc = new Db_Descriptor_Column();
		
		$this->assertSame($desc, $desc->setUpdatable(false));
		$this->assertEquals(false, $desc->isUpdatable());
		$this->assertSame($desc, $desc->setUpdatable(true));
		$this->assertEquals(true, $desc->isUpdatable());
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetLocalColumn()
	{
		$desc = new Db_Descriptor_Column();
		
		$desc->setProperty('prop');
		
		$this->assertEquals('a.prop', $desc->getLocalColumn('a'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetSourceColumn()
	{
		$desc = new Db_Descriptor_Column();
		
		$desc->setColumn('goo');
		$desc->setProperty('prop');
		
		$this->assertEquals('a.goo', $desc->getSourceColumn('a'));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testGetSelectCode()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with('t.col')->will($this->returnValue('esct.esccol'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with('a_prop')->will($this->returnValue('esca_prop'));
		
		$desc = new Db_Descriptor_Column();
		
		$desc->setColumn('col');
		$desc->setProperty('prop');
		
		$this->assertEquals('esct.esccol AS esca_prop', $desc->getSelectCode('t', 'a', $mock));
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetFromDataToObjectCode()
	{
		$desc = $this->getMock('Db_Descriptor_Column', array('getCastToPhpCode', 'getFromDataCode'));
		
		$desc->expects($this->once())->method('getFromDataCode')->with('$data', '$alias')->will($this->returnValue('$data->{$alias.\'prop\'}'));
		$desc->expects($this->once())->method('getCastToPhpCode')->with('$data->{$alias.\'prop\'}')->will($this->returnValue('(String) $data->{$alias.\'_prop\'}'));
		
		$desc->setColumn('col');
		$desc->setProperty('prop');
		
		$this->assertEquals('$o->prop = (String) $data->{$alias.\'_prop\'};', $desc->getFromDataToObjectCode('$o', '$data', '$alias'));
	}
}


/* End of file Column.php */
/* Location: ./tests/Db/Descriptor */