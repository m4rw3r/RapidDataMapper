<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Descriptor
 */
class Db_DescriptorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../lib/Db.php';
		
		Db::initAutoload();
		
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
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testApplyPlugin()
	{
		$desc = new Db_Descriptor();
		
		// create a mock without extending the existing class
		$p = $this->getMock('Db_Plugin', array('setDescriptor', 'init'), array(), 'Db_Plugin2', false, false, false);
		
		$p->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Db_Descriptor'));
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
		$desc = new Db_Descriptor();
		
		// create a mock without extending the existing class
		$p = $this->getMock('Db_Plugin', array('setDescriptor', 'init'), array(), 'Db_Plugin2', false, false, false);
		
		$p->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Db_Descriptor'));
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
		$desc = new Db_Descriptor();
		
		// create a mock without extending the existing class
		$p  = $this->getMock('Db_Plugin', array('setDescriptor', 'init', 'remove'), array(), 'Db_Plugin2', false, false, false);
		$p2 = new Db_Plugin2();
		
		$p->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Db_Descriptor'));
		$p->expects($this->once())->method('init');
		$p->expects($this->once())->method('remove');
		
		$p2->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Db_Descriptor'));
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
		$desc = new Db_Descriptor();
		
		// primary key and class name are needed for the getBuilder() method
		$desc->add($desc->newPrimaryKey('id'));
		$desc->setClass('stdClass');
		
		// create a mock without extending the existing class
		$p  = $this->getMock('Db_Plugin', array('setDescriptor', 'init', 'remove', 'editBuilder'), array(), 'Db_Plugin2', false, false, false);
		
		$p->expects($this->once())->method('setDescriptor')->with($this->isInstanceOf('Db_Descriptor'));
		$p->expects($this->once())->method('init');
		$p->expects($this->once())->method('editBuilder')->with($this->isInstanceOf('Db_CompiledBuilder'));
		
		$desc->applyPlugin($p);
		
		$this->assertThat($desc->getBuilder(), $this->isInstanceOf('Db_CompiledBuilder'));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testAddDecorator()
	{
		eval('class ConcreteDb_Decorator extends Db_Decorator {}');
		
		$desc = new Db_Descriptor();
		
		$c = $desc->newColumn('col');
		$r = $desc->newRelation('rel');
		$k = $desc->newPrimaryKey('pk');
		
		$desc->add($c)->add($r)->add($k);
		
		$d = new ConcreteDb_Decorator();
		$d->setDecoratedObject($c);
		
		$this->assertTrue($desc->addDecorator($d));
		
		$this->assertContainsOnly($d, $desc->getColumns());
		$this->assertContainsOnly($r, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$d2 = new ConcreteDb_Decorator();
		$d2->setDecoratedObject($r);
		
		$this->assertTrue($desc->addDecorator($d2));
		
		$this->assertContainsOnly($d, $desc->getColumns());
		$this->assertContainsOnly($d2, $desc->getRelations());
		$this->assertContainsOnly($k, $desc->getPrimaryKeys());
		
		$d3 = new ConcreteDb_Decorator();
		$d3->setDecoratedObject($k);
		
		$this->assertTrue($desc->addDecorator($d3));
		
		$this->assertContainsOnly($d, $desc->getColumns());
		$this->assertContainsOnly($d2, $desc->getRelations());
		$this->assertContainsOnly($d3, $desc->getPrimaryKeys());
		
		$s = new stdClass();
		
		$d4 = new ConcreteDb_Decorator();
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
		eval('class ConcreteDb_Decorator extends Db_Decorator {}');
		
		$desc = new Db_Descriptor();
		
		$c = $desc->newColumn('col');
		$r = $desc->newRelation('rel');
		$k = $desc->newPrimaryKey('pk');
		
		$desc->add($c)->add($r)->add($k);
		
		$d = new ConcreteDb_Decorator();
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
		eval('class ConcreteDb_Decorator extends Db_Decorator {}');
		
		$desc = new Db_Descriptor();
		
		$c = $desc->newColumn('col');
		$r = $desc->newRelation('rel');
		$k = $desc->newPrimaryKey('pk');
		
		$desc->add($c)->add($r)->add($k);
		
		$d = new ConcreteDb_Decorator();
		$d->setDecoratedObject($c);
		
		$desc->addDecorator($d);
		
		$d2 = new ConcreteDb_Decorator();
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
		eval('class ConcreteDb_Decorator extends Db_Decorator {}');
		
		$desc = new Db_Descriptor();
		
		$c = $desc->newColumn('col');
		$r = $desc->newRelation('rel');
		$k = $desc->newPrimaryKey('pk');
		
		$desc->add($c)->add($r)->add($k);
		
		$d = new ConcreteDb_Decorator();
		$d->setDecoratedObject($c);
		
		$desc->addDecorator($d);
		
		$d2 = new ConcreteDb_Decorator();
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
	 * @runInSeparateProcess enabled
	 * @preserveGlobalState disabled
	 */
	public function testGetBuilder3()
	{
		// Dummy class
		eval('class Db_CompiledBuilder
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
		
		$this->assertThat($b, $this->isInstanceOf('Db_CompiledBuilder'));
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
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksInvalidArray()
	{
		$desc = new Db_Descriptor();
		
		$desc->setHook('test', array('a', 'b', 'c'));
		$desc->getHookCode('test');
	}
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksInvalidArray2()
	{
		$desc = new Db_Descriptor();
		
		$desc->setHook('test', array('a', array()));
		$desc->getHookCode('test');
	}
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksInvalidArray3()
	{
		$desc = new Db_Descriptor();
		
		$desc->setHook('test', array(array()));
		$desc->getHookCode('test');
	}
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksInvalidArray4()
	{
		$desc = new Db_Descriptor();
		
		$desc->setHook('test', array());
		$desc->getHookCode('test');
	}
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksInvalidArray5()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('oatest_static', '$obj');
	}
	
	// ------------------------------------------------------------------------
	
	public function testHooksEmpty()
	{
		$desc = new Db_Descriptor();
		
		$this->assertSame('', $desc->getHookCode('test'));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksStaticWhenNonIsRequired()
	{
		$desc = $this->initHooks();
		
		$desc->setHook('test', 'test_static');
		$desc->getHookCode('test', '$obj');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Db_Exception_InvalidCallable
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
		$desc = new Db_Descriptor();
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
		
		$desc = new Db_Descriptor();
		
		$desc->setClass('test_hooks_on_obj');
		$desc->setHook('test');
		
		$this->assertEquals('$obj->test();', $desc->getHookCode('test', '$obj'));
		$this->assertEquals('$obj->test($param);', $desc->getHookCode('test', '$obj', '$param'));
	}
	public function testHooksDefaultCallable2()
	{
		$this->createDummyClasses();
		
		$desc = new Db_Descriptor();
		
		$desc->setClass('test_hooks_on_obj');
		$desc->setHook('test_static');
		
		$this->assertEquals('test_hooks_on_obj::test_static();', $desc->getHookCode('test_static'));
		$this->assertEquals('test_hooks_on_obj::test_static($param);', $desc->getHookCode('test_static', false, '$param'));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksProtected()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('ptest', '$obj');
	}
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksProtected2()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('test_pstatic');
	}
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksProtected3()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('private_test', '$obj');
	}
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksProtected4()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('test_private_static');
	}
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksProtected5()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('otest_pstatic');
	}
	/**
	 * @expectedException Db_Exception_InvalidCallable
	 */
	public function testHooksProtected6()
	{
		$desc = $this->initHooks();
		
		$desc->getHookCode('otest_private_static');
	}
	
	// ------------------------------------------------------------------------
	
	public function testHooksCall()
	{
		$desc = new Db_Descriptor();
		
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
		$desc = new Db_Descriptor();
		
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
	 * @return Db_Descriptor
	 */
	protected function initHooks()
	{
		$this->createDummyClasses();
		
		$desc = new Db_Descriptor();
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
/* Location: ./tests/Db */