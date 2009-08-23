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
 * @covers Db_Descriptor
 */
class Db_DescriptorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		// Dummy configuration
		Db::setConnectionConfig(
		    array(
		        'default' => array(
		            'hostname' => 'localhost',
		            'dbdriver' => 'mysql'
		        )
		    )
		);
	}
	
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
	public function testAdd4()
	{
		$desc = new Db_Descriptor();
		$c = new Db_Descriptor_Column();
		$c2 = new Db_Descriptor_Column();
		
		$c->setProperty('a');
		$c2->setProperty('a');
		
		$desc->add($c);
		$this->assertContainsOnly($c, $desc->getColumns());
		
		// replaces c2, as the property is the same
		$desc->add($c2);
		$this->assertContainsOnly($c2, $desc->getColumns());
	}
	public function testAdd5()
	{
		$desc = new Db_Descriptor();
		$pk = new Db_Descriptor_PrimaryKey();
		$pk2 = new Db_Descriptor_PrimaryKey();
		
		$pk->setProperty('a');
		$pk2->setProperty('a');
		
		$desc->add($pk);
		$this->assertContainsOnly($pk, $desc->getPrimaryKeys());
		
		// replaces pk2, as the property is the same
		$desc->add($pk2);
		$this->assertContainsOnly($pk2, $desc->getPrimaryKeys());
	}
	public function testAdd6()
	{
		$desc = new Db_Descriptor();
		$r = new Db_Descriptor_Relation();
		$r2 = new Db_Descriptor_Relation();
		
		$r->setProperty('a');
		$r2->setProperty('a');
		
		$desc->add($r);
		$this->assertContainsOnly($r, $desc->getRelations());
		
		// replaces r2, as the property is the same
		$desc->add($r2);
		$this->assertContainsOnly($r2, $desc->getRelations());
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
	
	// ------------------------------------------------------------------------
	
	public function testGetConnectionName()
	{
		$desc = new Db_Descriptor();
		
		$this->assertFalse($desc->getConnectionName(), 'Default value of getConnectionName() is false');
		
		$desc->setConnectionName('default');
		
		$this->assertSame('default', $desc->getConnectionName());
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetConnection()
	{
		$desc = new Db_Descriptor();
		
		$this->assertTrue($desc->getConnection() instanceof Db_Connection, 'Assert that we get a connection object');
		$this->assertEquals('default', $desc->getConnection()->getName());
		$this->assertSame(Db::getConnection(), $desc->getConnection());
		
		$c = $this->getMock('Db_Driver_Mysql_Connection', null, array('mock', array()));
		$desc->setConnection($c);
		
		$this->assertSame($c, $desc->getConnection());
	}
	public function testGetConnection2()
	{
		$desc = new Db_Descriptor();
		
		Db::setConnectionConfig(
			array(
				'Mock' => array(
					'dbdriver' => 'mysql'
					)
				)
			);
		
		$desc->setConnectionName('Mock');
		
		$this->assertEquals('Mock', $desc->getConnection()->getName());
		$this->assertSame(Db::getConnection('Mock'), $desc->getConnection());
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Db_Exception_Descriptor_MissingClassName
	 */
	public function testGetBuilder()
	{
		$desc = new Db_Descriptor();
		
		$desc->getBuilder();
	}
	/**
	 * @expectedException Db_Exception_MissingPrimaryKey
	 */
	public function testGetBuilder2()
	{
		$desc = new Db_Descriptor();
		$desc->setClass('Foobar');
		
		$desc->getBuilder();
	}
	/**
	 * @runInSeparateProcess
	 */
	public function testGetBuilder3()
	{
		// Dummy class
		eval('class Db_Mapper_Builder
		{
			protected $desc;
			public function __construct($desc)
				{ $this->desc = $desc; }
			public function getDesc()
				{ return $this->desc; }
		}');
		
		$desc = new Db_Descriptor();
		$desc->setClass('Foobar');
		$desc->add($desc->newPrimaryKey('id'));
		
		$b = $desc->getBuilder();
		
		$this->assertTrue($b instanceof Db_Mapper_Builder);
		$this->assertSame($desc, $b->getDesc());
		
		$b2 = $desc->getBuilder();
		
		$this->assertThat(
				$b,
				$this->logicalNot(
					$this->identicalTo($b2)
				)
			);
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @runInSeparateProcess
	 */
	public function testCreateBuilder()
	{
		eval('class TestDescriptor extends Db_Descriptor
		{
			protected $b;
			public function setBuilder($b)
				{ $this->builder = $b; }
			public function createBuilder()
				{ return $this->builder; }
		}');
		
		$desc = new TestDescriptor();
		$desc->setBuilder($dummy = new stdClass);
		$desc->setClass('Foobar');
		
		try
		{
			$desc->getBuilder();
			
			$this->fail('An expected Db_Exception_MissingPrimaryKey exception has not been raised.');
		}
		catch(Db_Exception_MissingPrimaryKey $e)
		{
			// just continue
		}
		
		$desc->add($desc->newPrimaryKey('id'));
		
		$this->assertSame($dummy, $desc->getBuilder());
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetUidCode()
	{
		$desc = new Db_Descriptor();
		$pk = $this->getMock('Db_Descriptor_PrimaryKey');
		$pk2 = $this->getMock('Db_Descriptor_PrimaryKey');
		
		
		$pk ->expects($this->exactly(2))
			->method('getFromDataCode')
			->with($this->equalTo('$data'), $this->equalTo('$alias'))
			->will($this->returnValue('pk1code'));
		
		$pk ->expects($this->any())
			->method('getProperty')
			->will($this->returnValue('a'));
		
		
		$pk2->expects($this->once())
			->method('getFromDataCode')
			->with($this->equalTo('$data'), $this->equalTo('$alias'))
			->will($this->returnValue('pk2code'));
		
		$pk2->expects($this->any())
			->method('getProperty')
			->will($this->returnValue('b'));
		
		
		$desc->add($pk);
		
		$this->assertEquals('pk1code', $desc->getUidCode('$data', '$alias'));
		
		$desc->add($pk2);
		
		$this->assertEquals('pk1code.\'*\'.pk2code', $desc->getUidCode('$data', '$alias'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetNotContainsObjectCode()
	{
		$desc = new Db_Descriptor();
		$pk = $this->getMock('Db_Descriptor_PrimaryKey');
		$pk2 = $this->getMock('Db_Descriptor_PrimaryKey');
		
		
		$pk ->expects($this->exactly(2))
			->method('getFromDataCode')
			->with($this->equalTo('$data'), $this->equalTo('$alias'))
			->will($this->returnValue('pk1code'));
		
		$pk ->expects($this->any())
			->method('getProperty')
			->will($this->returnValue('a'));
		
		
		$pk2->expects($this->once())
			->method('getFromDataCode')
			->with($this->equalTo('$data'), $this->equalTo('$alias'))
			->will($this->returnValue('pk2code'));
		
		$pk2->expects($this->any())
			->method('getProperty')
			->will($this->returnValue('b'));
		
		
		$desc->add($pk);
		
		$this->assertEquals('is_null(pk1code)', $desc->getNotContainsObjectCode('$data', '$alias'));
		
		$desc->add($pk2);
		
		$this->assertEquals('is_null(pk1code) OR is_null(pk2code)', $desc->getNotContainsObjectCode('$data', '$alias'));
	}
}


/* End of file Descriptor.php */
/* Location: ./tests/Db */