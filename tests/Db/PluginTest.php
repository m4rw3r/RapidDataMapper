<?php
/*
 * Created by Martin Wernståhl on 2009-09-04.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Plugin
 */
class Db_PluginTest extends PHPUnit_Framework_TestCase
{
	// ------------------------------------------------------------------------
	
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../lib/Db/Plugin.php';
		require_once dirname(__FILE__).'/../../lib/Db/Decorator.php';
		
		if( ! class_exists('ConcretePlugin', false))
		{
			eval('class ConcretePlugin extends Db_Plugin
			{
				public function __getDescriptor()
					{ return $this->descriptor; }
			}');
		}
		
		if( ! class_exists('ConcreteDecorator', false))
		{
			eval('class ConcreteDecorator extends Db_Decorator{ }');
		}
		
		if( ! class_exists('ConcreteDecorator2', false))
		{
			eval('class ConcreteDecorator2 extends Db_Decorator{ }');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function testIsAbstract()
	{
		$r = new ReflectionClass('Db_Plugin');
		
		$this->assertTrue($r->isAbstract());
	}
	
	// ------------------------------------------------------------------------
	
	public function testSetDescriptor()
	{
		$c = new ConcretePlugin();
		$c->setDescriptor($d = $this->getMock('Db_Descriptor'));
		
		$this->assertSame($d, $c->__getDescriptor());
	}
	
	// ------------------------------------------------------------------------

	public function testEmptyMethods()
	{
		$c = new ConcretePlugin();
		
		$c->init();
		$c->editBuilder(new stdClass);
		$c->remove();
		
	}
	
	// ------------------------------------------------------------------------
	
	public function testHasDecorator()
	{
		$this->assertFalse(Db_Plugin::hasDecorator(new stdClass, 'Db_Decorator'));
		
		$c = new stdClass();
		
		$d = new ConcreteDecorator();
		$d->setDecoratedObject($c);
		
		$this->assertTrue(Db_Plugin::hasDecorator($d, 'Db_Decorator'));
		$this->assertTrue(Db_Plugin::hasDecorator($d, 'ConcreteDecorator'));
		
		$this->assertFalse(Db_Plugin::hasDecorator($d, 'Some_Decorator'));
	}
	public function testHasDecorator2()
	{
		$c = new stdClass();
		
		$d = new ConcreteDecorator();
		$d->setDecoratedObject($c);
		
		$d2 = new ConcreteDecorator2();
		$d2->setDecoratedObject($d);
		
		$this->assertTrue(Db_Plugin::hasDecorator($d2, 'Db_Decorator'));
		$this->assertTrue(Db_Plugin::hasDecorator($d2, 'ConcreteDecorator'));
		$this->assertTrue(Db_Plugin::hasDecorator($d2, 'ConcreteDecorator2'));
		
		$this->assertFalse(Db_Plugin::hasDecorator($d2, 'Some_Decorator'));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testHasDecoratorInvalidArgument()
	{
		Db_Plugin::hasDecorator('foobar', 'Some_class');
	}
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testHasDecoratorInvalidArgument2()
	{
		Db_Plugin::hasDecorator(array(), 'Some_class');
	}
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testHasDecoratorInvalidArgument3()
	{
		Db_Plugin::hasDecorator(array('foobar'), 'Some_class');
	}
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testHasDecoratorInvalidArgument4()
	{
		Db_Plugin::hasDecorator(123, 'Some_class');
	}
}

/* End of file PluginTest.php */
/* Location: ./tests/Db */