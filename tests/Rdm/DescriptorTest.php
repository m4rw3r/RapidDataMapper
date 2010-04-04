<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * @covers Rdm_Descriptor
 * @runTestsInSeparateThreads
 */
class Rdm_DescriptorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{	
		require_once dirname(__FILE__).'/../../lib/Rdm/Config.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Adapter.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Exception.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Descriptor.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Descriptor/Column.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Descriptor/Exception.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Descriptor/MissingValueException.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Descriptor/PrimaryKey.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Descriptor/Relation.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Util/Decorator.php';
		require_once dirname(__FILE__).'/../../lib/Rdm/Util/Inflector.php';
		
		class_exists('MockAdapter') OR $this->getMock('Rdm_Adapter', null, array(), 'MockAdapter', false);
		class_exists('Rdm_Builder_Main') OR $this->getMock('Rdm_Builder_Main');
		
		Rdm_Config::setAdapterConfiguration('default', array('class' => 'MockAdapter'));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassName()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->getClass();
	}
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassName2()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->getSingular();
	}
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassName3()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->getTable();
	}
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassName4()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->getFactory();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassNameButSingular()
	{
		$desc = new Rdm_Descriptor();
		$desc->setSingular('test');
		
		$desc->getClass();
	}
	public function testNoClassNameButSingular2()
	{
		$desc = new Rdm_Descriptor();
		$desc->setSingular('test');
		
		// these build on singular
		$this->assertEquals($desc->getSingular(), 'test');
		$this->assertEquals($desc->getTable(), 'tests');
	}
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassNameButSingular3()
	{
		$desc = new Rdm_Descriptor();
		$desc->setSingular('test');
		
		$desc->getFactory();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassNameButTable()
	{
		$desc = new Rdm_Descriptor();
		$desc->setTable('tests');
		
		$desc->getClass();
	}
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassNameButTable2()
	{
		$desc = new Rdm_Descriptor();
		$desc->setTable('tests');
		
		$desc->getSingular();
	}
	public function testNoClassNameButTable3()
	{
		$desc = new Rdm_Descriptor();
		$desc->setTable('tests');
		
		$this->assertEquals($desc->getTable(), 'tests');
	}
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassNameButTable4()
	{
		$desc = new Rdm_Descriptor();
		$desc->setTable('tests');
		
		$desc->getFactory();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassNameButFactory()
	{
		$desc = new Rdm_Descriptor();
		$desc->setFactory('new Foobar');
		
		$desc->getClass();
	}
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassNameButFactory2()
	{
		$desc = new Rdm_Descriptor();
		$desc->setFactory('new Foobar');
		
		$desc->getSingular();
	}
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testNoClassNameButFactory3()
	{
		$desc = new Rdm_Descriptor();
		$desc->setFactory('new Foobar');
		
		$desc->getTable();
	}
	public function testNoClassNameButFactory4()
	{
		$desc = new Rdm_Descriptor();
		$desc->setFactory('new Foobar');
		
		$this->assertEquals($desc->getFactory(), 'new Foobar');
	}
	
	// ------------------------------------------------------------------------

	public function testSetClass()
	{
		$desc = new Rdm_Descriptor();
		$desc->setClass('Test');
		
		$this->assertEquals($desc->getClass(), 'Test');
		$this->assertEquals($desc->getSingular(), 'test');
		$this->assertEquals($desc->getTable(), 'tests');
		$this->assertEquals($desc->getFactory(), 'new Test');
	}
	
	// ------------------------------------------------------------------------
	
	public function testAutoSetClass()
	{
		if( ! class_exists('DummyTestAutoSetClassDescriptor'))
		{
			eval('class DummyTestAutoSetClassDescriptor extends Rdm_Descriptor {}');
		}
		
		$desc = new DummyTestAutoSetClassDescriptor();
		
		$this->assertEquals('DummyTestAutoSetClass', $desc->getClass());
		$this->assertEquals('dummytestautosetclass', $desc->getSingular());
		
		$desc->setClass('Test');
		$this->assertEquals('test', $desc->getSingular());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAdd()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->add(new stdClass);
	}
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAdd2()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->add('foo');
	}
	public function testAdd3()
	{
		$desc = new Rdm_Descriptor();
		$c = $this->getMock('Rdm_Descriptor_Column');
		$pk = $this->getMock('Rdm_Descriptor_PrimaryKey');
		$rel = $this->getMock('Rdm_Descriptor_Relation');
		
		$c->expects($this->once())->method('getProperty')->will($this->returnValue('foo'));
		$pk->expects($this->once())->method('getProperty')->will($this->returnValue('bar'));
		$rel->expects($this->once())->method('getProperty')->will($this->returnValue('baz'));
		
		$desc->add($c);
		$desc->add($pk);
		$desc->add($rel);
		
		$this->assertContainsOnly($c, $desc->getColumns());
		$this->assertContainsOnly($pk, $desc->getPrimaryKeys());
		$this->assertContainsOnly($rel, $desc->getRelations());
	}
	public function testAdd4()
	{
		$desc = new Rdm_Descriptor();
		$c = $this->getMock('Rdm_Descriptor_Column');
		$c2 = $this->getMock('Rdm_Descriptor_Column');
		
		$c->expects($this->once())->method('getProperty')->will($this->returnValue('foo'));
		$c2->expects($this->once())->method('getProperty')->will($this->returnValue('foo'));
		
		$desc->add($c);
		$this->assertContainsOnly($c, $desc->getColumns());
		
		// replaces c2, as the property is the same
		$desc->add($c2);
		$this->assertContainsOnly($c2, $desc->getColumns());
	}
	public function testAdd5()
	{
		$desc = new Rdm_Descriptor();
		$pk = $this->getMock('Rdm_Descriptor_PrimaryKey');
		$pk2 = $this->getMock('Rdm_Descriptor_PrimaryKey');
		
		$pk->expects($this->once())->method('getProperty')->will($this->returnValue('foo'));
		$pk2->expects($this->once())->method('getProperty')->will($this->returnValue('foo'));
		
		$desc->add($pk);
		$this->assertContainsOnly($pk, $desc->getPrimaryKeys());
		
		// replaces pk2, as the property is the same
		$desc->add($pk2);
		$this->assertContainsOnly($pk2, $desc->getPrimaryKeys());
	}
	public function testAdd6()
	{
		$desc = new Rdm_Descriptor();
		$r = $this->getMock('Rdm_Descriptor_Relation');
		$r2 = $this->getMock('Rdm_Descriptor_Relation');
		
		$r->expects($this->once())->method('getProperty')->will($this->returnValue('foo'));
		$r2->expects($this->once())->method('getProperty')->will($this->returnValue('foo'));
		
		$desc->add($r);
		$this->assertContainsOnly($r, $desc->getRelations());
		
		// replaces r2, as the property is the same
		$desc->add($r2);
		$this->assertContainsOnly($r2, $desc->getRelations());
	}
	
	// ------------------------------------------------------------------------
	
	public function testNewColumn()
	{
		$desc = new Rdm_Descriptor();
		
		$this->assertTrue($desc->newColumn('title') instanceof Rdm_Descriptor_Column);
	}
	
	// ------------------------------------------------------------------------
	
	public function testNewPrimaryKey()
	{
		$desc = new Rdm_Descriptor();
		
		$this->assertTrue($desc->newPrimaryKey('id') instanceof Rdm_Descriptor_PrimaryKey);
	}
	
	// ------------------------------------------------------------------------
	
	public function testNewRelation()
	{
		$desc = new Rdm_Descriptor();
		
		$this->assertTrue($desc->newRelation('image') instanceof Rdm_Descriptor_Relation);
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetAdapterName()
	{
		$desc = new Rdm_Descriptor();
		
		$this->assertFalse($desc->getAdapterName(), 'Default value of getAdapterName() is false');
		
		$desc->setAdapterName('default');
		
		$this->assertSame('default', $desc->getAdapterName());
	}
	public function testGetAdapterName2()
	{
		$desc = new Rdm_Descriptor();
		
		$c = $this->getMock('Rdm_Adapter', array('getName'), array(), '', false);
		$c->expects($this->once())->method('getName')->will($this->returnValue('mock'));
		$desc->setAdapter($c);
		
		$this->assertEquals('mock', $desc->getAdapterName());
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetAdapter()
	{
		$desc = new Rdm_Descriptor();
		
		$this->assertTrue($desc->getAdapter() instanceof Rdm_Adapter, 'Assert that we get a connection object');
		$this->assertEquals('default', $desc->getAdapter()->getName());
		$this->assertSame(Rdm_Adapter::getInstance(), $desc->getAdapter());
		
		$c = $this->getMock('Rdm_Adapter', null, array('mock', array()), '', false);
		$desc->setAdapter($c);
		
		$this->assertSame($c, $desc->getAdapter());
	}
	public function testGetAdapter2()
	{
		$desc = new Rdm_Descriptor();
		
		Rdm_Config::setAdapterConfiguration(
			array(
				'Mock' => array(
					'class' => 'MockAdapter'
					)
				)
			);
		
		$desc->setAdapterName('Mock');
		
		$this->assertEquals('Mock', $desc->getAdapter()->getName());
		$this->assertSame(Rdm_Adapter::getInstance('Mock'), $desc->getAdapter());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testApplyPlugin()
	{
		$desc = new Rdm_Descriptor();
		
		// create a mock without extending the existing class
		$p = $this->getMock('Rdm_Plugin', array('setDescriptor', 'init'), array(), 'Rdm_Plugin2', false, false, false);
		
		$p->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Rdm_Descriptor'));
		$p->expects($this->once())->method('init');
		
		$this->assertSame($desc, $desc->applyPlugin($p));
		
		$this->assertContainsOnly($p, $desc->getPlugins());
	}
	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testApplyPlugin2()
	{
		$desc = new Rdm_Descriptor();
		
		// create a mock without extending the existing class
		$p = $this->getMock('Rdm_Plugin', array('setDescriptor', 'init'), array(), 'Rdm_Plugin2', false, false, false);
		
		$p->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Rdm_Descriptor'));
		$p->expects($this->once())->method('init');
		
		$this->assertSame($desc, $desc->applyPlugin($p));
		$this->assertSame($desc, $desc->applyPlugin($p));
		
		$this->assertContainsOnly($p, $desc->getPlugins());
	}
	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testApplyPlugin3()
	{
		$desc = new Rdm_Descriptor();
		
		// create a mock without extending the existing class
		$p  = $this->getMock('Rdm_Plugin', array('setDescriptor', 'init', 'remove'), array(), 'Rdm_Plugin2', false, false, false);
		$p2 = new Rdm_Plugin2();
		
		$p->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Rdm_Descriptor'));
		$p->expects($this->once())->method('init');
		$p->expects($this->once())->method('remove');
		
		$p2->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Rdm_Descriptor'));
		$p2->expects($this->once())->method('init');
		$p2->expects($this->never())->method('remove');
		
		$this->assertSame($desc, $desc->applyPlugin($p));
		$this->assertSame($desc, $desc->applyPlugin($p2));
		
		$this->assertContainsOnly($p2, $desc->getPlugins());
	}
	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testPluginCallEditBuilder()
	{
		$desc = new Rdm_Descriptor();
		
		// primary key and class name are needed for the getBuilder() method
		$desc->add($desc->newPrimaryKey('id'));
		$desc->setClass('stdClass');
		
		// create a mock without extending the existing class
		$p  = $this->getMock('Rdm_Plugin', array('setDescriptor', 'init', 'remove', 'editBuilder'), array(), 'Rdm_Plugin2', false, false, false);
		
		$p->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Rdm_Descriptor'));
		$p->expects($this->once())->method('init');
		$p->expects($this->once())->method('editBuilder')->with($this->isInstanceOf('Rdm_Builder_Main'));
		
		$desc->applyPlugin($p);
		
		$this->assertThat($desc->getBuilder(), $this->isInstanceOf('Rdm_Builder_Main'));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testAddDecorator()
	{
		eval('class ConcreteRdm_Util_Decorator extends Rdm_Util_Decorator {}');
		
		$desc = new Rdm_Descriptor();
		
		$c = $desc->newColumn('col');
		$r = $desc->newRelation('rel');
		$k = $desc->newPrimaryKey('pk');
		
		$desc->add($c)->add($r)->add($k);
		
		$d = new ConcreteRdm_Util_Decorator();
		$d->setDecoratedObject($c);
		
		$this->assertTrue($desc->addDecorator($d));
		
		$this->assertContainsOnly($d, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$d2 = new ConcreteRdm_Util_Decorator();
		$d2->setDecoratedObject($r);
		
		$this->assertTrue($desc->addDecorator($d2));
		
		$this->assertContainsOnly($d, $desc->getColumns());
		$this->assertContainsOnly($d2, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$d3 = new ConcreteRdm_Util_Decorator();
		$d3->setDecoratedObject($k);
		
		$this->assertTrue($desc->addDecorator($d3));
		
		$this->assertContainsOnly($d, $desc->getColumns());
		$this->assertContainsOnly($d2, $desc->getRelations());
		$this->assertContainsOnly($d3, $desc->getPrimaryKeys());
		
		$s = new stdClass();
		
		$d4 = new ConcreteRdm_Util_Decorator();
		$d4->setDecoratedObject($s);
		
		$this->assertFalse($desc->addDecorator($d4));
		
		$this->assertContainsOnly($d, $desc->getColumns());
		$this->assertContainsOnly($d2, $desc->getRelations());
		$this->assertContainsOnly($d3, $desc->getPrimaryKeys());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testRemoveDecorator()
	{
		eval('class ConcreteRdm_Util_Decorator extends Rdm_Util_Decorator {}');
		
		$desc = new Rdm_Descriptor();
		
		$c = $desc->newColumn('col');
		$r = $desc->newRelation('rel');
		$k = $desc->newPrimaryKey('pk');
		
		$desc->add($c)->add($r)->add($k);
		
		$d = new ConcreteRdm_Util_Decorator();
		$d->setDecoratedObject($c);
		
		$desc->addDecorator($d);
		
		$this->assertContainsOnly($d, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$this->assertTrue($desc->removeDecorator($d));
		
		$this->assertContainsOnly($c, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$this->assertFalse($desc->removeDecorator($d));
		
		// TODO: Add tests for relations and primary keys, needed? (not for the moment, AFAIK)
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testRemoveDecoratorFromChain()
	{
		eval('class ConcreteRdm_Util_Decorator extends Rdm_Util_Decorator {}');
		
		$desc = new Rdm_Descriptor();
		
		$c = $desc->newColumn('col');
		$r = $desc->newRelation('rel');
		$k = $desc->newPrimaryKey('pk');
		
		$desc->add($c)->add($r)->add($k);
		
		$d = new ConcreteRdm_Util_Decorator();
		$d->setDecoratedObject($c);
		
		$desc->addDecorator($d);
		
		$d2 = new ConcreteRdm_Util_Decorator();
		$d2->setDecoratedObject($d);
		
		$desc->addDecorator($d2);
		
		$this->assertContainsOnly($d2, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$this->assertTrue($desc->removeDecorator($d2));
		
		$this->assertContainsOnly($d, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$this->assertFalse($desc->removeDecorator($d2));
		
		$this->assertTrue($desc->removeDecorator($d));
		
		$this->assertContainsOnly($c, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$this->assertFalse($desc->removeDecorator($d));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testRemoveDecoratorFromChain2()
	{
		eval('class ConcreteRdm_Util_Decorator extends Rdm_Util_Decorator {}');
		
		$desc = new Rdm_Descriptor();
		
		$c = $desc->newColumn('col');
		$r = $desc->newRelation('rel');
		$k = $desc->newPrimaryKey('pk');
		
		$desc->add($c)->add($r)->add($k);
		
		$d = new ConcreteRdm_Util_Decorator();
		$d->setDecoratedObject($c);
		
		$desc->addDecorator($d);
		
		$d2 = new ConcreteRdm_Util_Decorator();
		$d2->setDecoratedObject($d);
		
		$desc->addDecorator($d2);
		
		$this->assertContainsOnly($d2, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		// remove the middle decorator
		$this->assertTrue($desc->removeDecorator($d));
		
		$this->assertContainsOnly($d2, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		// check if the column has been moved from $d to $d2
		$this->assertSame($c, $d2->getDecoratedObject());
		
		$this->assertFalse($desc->removeDecorator($d));
		
		$this->assertTrue($desc->removeDecorator($d2));
		
		$this->assertContainsOnly($c, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$this->assertFalse($desc->removeDecorator($d2));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testGetBuilder()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->getBuilder();
	}
	/**
	 * @expectedException Rdm_Descriptor_MissingValueException
	 */
	public function testGetBuilder2()
	{
		$desc = new Rdm_Descriptor();
		$desc->setClass('Foobar');
		
		$desc->getBuilder();
	}
	public function testGetBuilder3()
	{
		// Dummy class
		eval('class Rdm_Builder_Main_TestGetBuilderMock
		{
			protected $desc;
			public function __construct($desc)
				{ $this->desc = $desc; }
			public function getDesc()
				{ return $this->desc; }
		}
		
		class Rdm_Descriptor_TestGetBuilderMock extends Rdm_Descriptor
		{
			public function createBuilder()
			{
				return new Rdm_Builder_Main_TestGetBuilderMock($this);
			}
		}');
		
		$desc = new Rdm_Descriptor_TestGetBuilderMock;
		$desc->setClass('Foobar');
		$desc->add($desc->newPrimaryKey('id'));
		
		$b = $desc->getBuilder();
		
		$this->assertThat($b, $this->isInstanceOf('Rdm_Builder_Main_TestGetBuilderMock'));
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
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testCreateBuilder()
	{
		eval('class TestDescriptor extends Rdm_Descriptor
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
			
			$this->fail('An expected Rdm_Exception_MissingPrimaryKey exception has not been raised.');
		}
		catch(Rdm_Descriptor_MissingValueException $e)
		{
			$this->assertEquals('primary key', $e->getValueName());
			// just continue
		}
		
		$desc->add($desc->newPrimaryKey('id'));
		
		$this->assertSame($dummy, $desc->getBuilder());
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetUidCode()
	{
		$desc = new Rdm_Descriptor();
		$pk = $this->getMock('Rdm_Descriptor_PrimaryKey');
		$pk2 = $this->getMock('Rdm_Descriptor_PrimaryKey');
		
		
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
		$desc = new Rdm_Descriptor();
		$pk = $this->getMock('Rdm_Descriptor_PrimaryKey');
		$pk2 = $this->getMock('Rdm_Descriptor_PrimaryKey');
		
		
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
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksInvalidArray()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->setHook('test', array('a', 'b', 'c'));
		$desc->getHookCode('test');
	}
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksInvalidArray2()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->setHook('test', array('a', array()));
		$desc->getHookCode('test');
	}
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksInvalidArray3()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->setHook('test', array(array()));
		$desc->getHookCode('test');
	}
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksInvalidArray4()
	{
		$desc = new Rdm_Descriptor();
		
		$desc->setHook('test', array());
		$desc->getHookCode('test');
	}
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksInvalidArray5()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('oatest_static', '$obj');
	}
	
	// ------------------------------------------------------------------------
	
	public function testHooksEmpty()
	{
		$desc = new Rdm_Descriptor();
		
		$this->assertSame('', $desc->getHookCode('test'));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksStaticWhenNonIsRequired()
	{
		$desc = $this->initHooks();
		
		$desc->setHook('test', 'test_static');
		$desc->getHookCode('test', '$obj');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksMissingMethod()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('missing_func', '$obj');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException ReflectionException
	 */
	public function testHooksMissingClass()
	{
		// TODO: Correct when a proper excepton replaces ReflectionException
		$desc = new Rdm_Descriptor();
		$desc->setClass('Some_strange_missing_class');
		
		$desc->setHook('foo', 'foo');
		$desc->getHookCode('foo', '$obj');
	}
	
	// ------------------------------------------------------------------------
	
	public function testHooks()
	{
		$desc = $this->initHooks();
		
		$this->assertEquals('$obj->test();', $desc->getHookCode('test', '$obj'));
		$this->assertEquals('$obj->test($param);', $desc->getHookCode('test', '$obj', '$param'));
	}
	public function testHooks2()
	{
		$desc = $this->initHooks();
		
		$this->assertEquals('test_hooks_on_obj::test_static();', $desc->getHookCode('test_static'));
		$this->assertEquals('test_hooks_on_obj::test_static($param);', $desc->getHookCode('test_static', false, '$param'));
	}
	public function testHooks3()
	{
		$desc = $this->initHooks();
		
		$this->assertEquals('Other_class::test_static();', $desc->getHookCode('otest_static'));
		$this->assertEquals('Other_class::test_static($param);', $desc->getHookCode('otest_static', false, '$param'));
		$this->assertEquals('Other_class::test_static();', $desc->getHookCode('oatest_static'));
		$this->assertEquals('Other_class::test_static($param);', $desc->getHookCode('oatest_static', false, '$param'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testHooksDefaultCallable4()
	{
		$this->createDummyClasses();
		
		$desc = new Rdm_Descriptor();
		
		$desc->setClass('test_hooks_on_obj');
		$desc->setHook('test');
		
		$this->assertEquals('$obj->test();', $desc->getHookCode('test', '$obj'));
		$this->assertEquals('$obj->test($param);', $desc->getHookCode('test', '$obj', '$param'));
	}
	public function testHooksDefaultCallable2()
	{
		$this->createDummyClasses();
		
		$desc = new Rdm_Descriptor();
		
		$desc->setClass('test_hooks_on_obj');
		$desc->setHook('test_static');
		
		$this->assertEquals('test_hooks_on_obj::test_static();', $desc->getHookCode('test_static'));
		$this->assertEquals('test_hooks_on_obj::test_static($param);', $desc->getHookCode('test_static', false, '$param'));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksProtected()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('ptest', '$obj');
	}
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksProtected2()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('test_pstatic');
	}
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksProtected3()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('private_test', '$obj');
	}
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksProtected4()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('test_private_static');
	}
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksProtected5()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('otest_pstatic');
	}
	/**
	 * @expectedException Rdm_Descriptor_Exception
	 */
	public function testHooksProtected6()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('otest_private_static');
	}
	
	// ------------------------------------------------------------------------
	
	public function testHooksCall()
	{
		$desc = new Rdm_Descriptor();
		
		eval('class Some_foo
		{
			public function __call($m, $p){}
		}');
		
		$desc->setHook('t', 'test');
		$desc->setClass('Some_foo');
		
		$this->assertEquals('$obj->test();', $desc->getHookCode('t', '$obj'));
		$this->assertEquals('$obj->test($foo);', $desc->getHookCode('t', '$obj', '$foo'));
	}
	
	public function testHooksCallStatic()
	{
		$desc = new Rdm_Descriptor();
		
		eval('class Some_fooS
		{
			public static function __callStatic($m, $p){}
		}');
		
		$desc->setHook('t', 'test');
		$desc->setClass('Some_fooS');
		
		$this->assertEquals('Some_fooS::test();', $desc->getHookCode('t'));
		$this->assertEquals('Some_fooS::test($foo);', $desc->getHookCode('t', false, '$foo'));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Initializes a set of hooks in a descriptor.
	 * 
	 * @return Rdm_Descriptor
	 */
	protected function initHooks()
	{
		$this->createDummyClasses();
		
		$desc = new Rdm_Descriptor();
		$desc->setClass('test_hooks_on_obj');
		
		$desc->setHook('test', 'test');
		$desc->setHook('ptest', 'ptest');
		$desc->setHook('private_test', 'private_test');
		$desc->setHook('test_static', 'test_static');
		$desc->setHook('test_pstatic', 'test_pstatic');
		$desc->setHook('test_private_static', 'test_private_static');
		
		$desc->setHook('otest_static', 'Other_class::test_static');
		$desc->setHook('otest_pstatic', 'Other_class::test_pstatic');
		$desc->setHook('otest_private_static', 'Other_class::test_private_static');
		
		$desc->setHook('oatest_static', array('Other_class', 'test_static'));
		$desc->setHook('oatest_pstatic', array('Other_class', 'test_pstatic'));
		$desc->setHook('oatest_private_static', array('Other_class', 'test_private_static'));
		
		$desc->setHook('missing_func', 'missing_func');
		
		return $desc;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates dummy classes which can be used to test the hooks on
	 * 
	 * @return void
	 */
	protected function createDummyClasses()
	{
		if( ! class_exists('test_hooks_on_obj'))
		{
			eval('class test_hooks_on_obj
			{
				public function test(){}
				protected function ptest(){}
				protected function private_test(){}
				public static function test_static(){}
				protected static function test_pstatic(){}
				protected static function test_private_static(){}
			}
			class Other_class
			{	
				public static function test_static(){}
				protected static function test_pstatic(){}
				protected static function test_private_static(){}
			}');
		}
	}
}


/* End of file DescriptorTest.php */
/* Location: ./tests/Rdm */