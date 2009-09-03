<?php
/*
 * Created by Martin Wernståhl on 2009-09-02.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Decorator
 */
class Db_DecoratorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../lib/Db.php';
		
		Db::initAutoload();
		
		if( ! class_exists('ConcreteDecorator'))
		{
			eval('class ConcreteDecorator extends Db_Decorator{ }');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function testAbstract()
	{
		$r = new ReflectionClass('Db_Decorator');
		
		$this->assertTrue($r->isAbstract());
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException RuntimeException
	 */
	public function testGetDecoratedObject()
	{
		$d = new ConcreteDecorator();
		
		$d->getDecoratedObject();
	}
	/**
	 * @expectedException RuntimeException
	 */
	public function testGetDecoratedObject2()
	{
		$d = new ConcreteDecorator();
		
		$d->setDecoratedObject('foobar');
		
		$d->getDecoratedObject();
	}
	/**
	 * @expectedException RuntimeException
	 */
	public function testGetDecoratedObject3()
	{
		$d = new ConcreteDecorator();
		
		$d->setDecoratedObject(234);
		
		$d->getDecoratedObject();
	}
	/**
	 * @expectedException RuntimeException
	 */
	public function testGetDecoratedObject4()
	{
		$d = new ConcreteDecorator();
		
		$d->setDecoratedObject(array('foobar', 234, 'baz'));
		
		$d->getDecoratedObject();
	}
	
	// ------------------------------------------------------------------------
	
	public function testSetDecoratedObject()
	{
		$d = new ConcreteDecorator();
		
		$o = new stdClass;
		
		$d->setDecoratedObject($o);
		
		$this->assertSame($o, $d->getDecoratedObject());
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException BadMethodCallException
	 */
	public function testDecorate()
	{
		$d = new ConcreteDecorator();
		
		$o = new stdClass();
		
		$d->setDecoratedObject($o);
		
		$d->some_method();
	}
	public function testDecorate2()
	{
		$d = new ConcreteDecorator();
		
		$o = $this->getMock('stdClass', array('test'));
		$o->expects($this->once())->method('test')->with($this->equalTo('foobar'))->will($this->returnValue('test_result'));
		
		$d->setDecoratedObject($o);
		
		$this->assertEquals('test_result', $d->test('foobar'));
	}
	public function testDecorate3()
	{
		$d = new ConcreteDecorator();
		
		eval('class TestMock
		{
			function __call($method, $params)
			{ }
		}');
		
		$o = $this->getMock('TestMock', array('__call'));
		
		$o->expects($this->once())->method('__call')->with($this->equalTo('test'), $this->equalTo(array('foobar')))->will($this->returnValue('test_result'));
		
		$d->setDecoratedObject($o);
		
		$this->assertEquals('test_result',  $d->test('foobar'));
	}
	public function testDecorate4()
	{
		eval('class TestDecorator extends Db_Decorator
		{
			function test()
			{
				return "from decorator";
			}
		}');
		
		$d = new TestDecorator();
		
		$o = $this->getMock('stdClass', array('test', 'test2'));
		
		$o->expects($this->never())->method('test');
		$o->expects($this->once())->method('test2')->with('foobar2')->will($this->returnValue('from decorated'));
		
		$d->setDecoratedObject($o);
		
		$this->assertEquals('from decorator', $d->test());
		$this->assertEquals('from decorated', $d->test2('foobar2'));
	}
}


/* End of file DecoratorTest.php */
/* Location: ./tests/Db */