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
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassNameButSingular()
	{
		$desc = new Db_Descriptor();
		$desc->setSingular('test');
		
		$desc->getClass();
	}
	public function testNoClassNameButSingular2()
	{
		$desc = new Db_Descriptor();
		$desc->setSingular('test');
		
		// these build on singular
		$this->assertEquals($desc->getSingular(), 'test');
		$this->assertEquals($desc->getTable(), 'tests');
	}
	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassNameButSingular3()
	{
		$desc = new Db_Descriptor();
		$desc->setSingular('test');
		
		$desc->getFactory();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassNameButTable()
	{
		$desc = new Db_Descriptor();
		$desc->setTable('tests');
		
		$desc->getClass();
	}
	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassNameButTable2()
	{
		$desc = new Db_Descriptor();
		$desc->setTable('tests');
		
		$desc->getSingular();
	}
	public function testNoClassNameButTable3()
	{
		$desc = new Db_Descriptor();
		$desc->setTable('tests');
		
		$this->assertEquals($desc->getTable(), 'tests');
	}
	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassNameButTable4()
	{
		$desc = new Db_Descriptor();
		$desc->setTable('tests');
		
		$desc->getFactory();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassNameButFactory()
	{
		$desc = new Db_Descriptor();
		$desc->setFactory('new Foobar');
		
		$desc->getClass();
	}
	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassNameButFactory2()
	{
		$desc = new Db_Descriptor();
		$desc->setFactory('new Foobar');
		
		$desc->getSingular();
	}
	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testNoClassNameButFactory3()
	{
		$desc = new Db_Descriptor();
		$desc->setFactory('new Foobar');
		
		$desc->getTable();
	}
	public function testNoClassNameButFactory4()
	{
		$desc = new Db_Descriptor();
		$desc->setFactory('new Foobar');
		
		$this->assertEquals($desc->getFactory(), 'new Foobar');
	}
	
	// ------------------------------------------------------------------------

	public function testSetClass()
	{
		$desc = new Db_Descriptor();
		$desc->setClass('Test');
		
		$this->assertEquals($desc->getClass(), 'Test');
		$this->assertEquals($desc->getSingular(), 'test');
		$this->assertEquals($desc->getTable(), 'tests');
		$this->assertEquals($desc->getFactory(), 'new Test');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAdd()
	{
		$desc = new Db_Descriptor();
		
		$desc->add(new stdClass);
	}
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAdd2()
	{
		$desc = new Db_Descriptor();
		
		$desc->add('foo');
	}
	public function testAdd3()
	{
		$desc = new Db_Descriptor();
		$c = new Db_Descriptor_Column();
		$pk = new Db_Descriptor_PrimaryKey();
		$rel = new Db_Descriptor_Relation();
		
		$c->setProperty('foo');
		$pk->setProperty('bar');
		$rel->setProperty('baz');
		
		$desc->add($c);
		$desc->add($pk);
		$desc->add($rel);
		
		$this->assertContainsOnly($c, $desc->getColumns());
		$this->assertContainsOnly($pk, $desc->getPrimaryKeys());
		$this->assertContainsOnly($rel, $desc->getRelations());
	}
	
	// ------------------------------------------------------------------------
	
	public function testNewColumn()
	{
		$desc = new Db_Descriptor();
		
		$this->assertTrue($desc->newColumn('title') instanceof Db_Descriptor_Column);
	}
	
	// ------------------------------------------------------------------------
	
	public function testNewPrimaryKey()
	{
		$desc = new Db_Descriptor();
		
		$this->assertTrue($desc->newPrimaryKey('id') instanceof Db_Descriptor_PrimaryKey);
	}
	
	// ------------------------------------------------------------------------
	
	public function testNewRelation()
	{
		$desc = new Db_Descriptor();
		
		$this->assertTrue($desc->newRelation('image') instanceof Db_Descriptor_Relation);
	}
}


/* End of file Descriptor.php */
/* Location: ./tests/Db */