<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db
 * @runTestsInSeparateProcesses enabled
 * @preserveGlobalState disabled
 */
class DbTest extends PHPUnit_Framework_TestCase
{
	
	public $preserveGlobalState = false;
	
	public function setUp()
	{
		require_once dirname(__FILE__).'/../lib/Db.php';
		
		Db::initAutoload();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Instantiation of Db class is not allowed.
	 */
	public function testClassInstantiation()
	{
		$reflection = new ReflectionClass('Db');
		
		$sum = false;
		
		$sum = ($sum OR $reflection->isAbstract());
		
		$c = $reflection->getConstructor();
		
		$sum = ($sum OR $c->isPrivate() OR $c->isProtected());
		
		$this->assertTrue($sum);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Db_Connection_ConfigurationException
	 */
	public function testNoConnection()
	{
		Db::getConnection();
	}
	/**
	 * @expectedException Db_Connection_ConfigurationException
	 */
	public function testNoConnection2()
	{
		Db::setConnectionConfig('foobar', array('something' => 'to satisfy test'));
		
		Db::getConnection();
	}
	/**
	 * @expectedException Db_Connection_ConfigurationException
	 */
	public function testSetConnectionConfigInvalid()
	{
		Db::setConnectionConfig('testing');
	}
	/**
	 * @expectedException Db_Connection_ConfigurationException
	 */
	public function testSetConnectionConfigInvalid2()
	{
		Db::setConnectionConfig(null);
	}
	/**
	 * @expectedException Db_Connection_ConfigurationException
	 */
	public function testSetConnectionConfigInvalid3()
	{
		Db::setConnectionConfig('test', array());
	}
	
	public function testGetDefaultConnection()
	{
		Db::setConnectionConfig('default', array('dbdriver' => 'mock'));
		
		eval('class Db_Driver_Mock_Connection {}');
		
		$this->assertTrue(Db::getConnection() instanceof Db_Driver_Mock_Connection);
	}
	/**
	 * @expectedException Db_Connection_ConfigurationException
	 */
	public function testSetDefaultConnectionName()
	{
		Db::setConnectionConfig('default', array('something' => 'to satisfy test'));
		
		Db::setDefaultConnectionName('foobar');
		
		Db::getConnection();
	}
	
	public function testSetConnectionConfig()
	{
		Db::setConnectionConfig(array());
	}
	
	public function testGetConnection()
	{
		Db::setConnectionConfig(array('foobar' => array('dbdriver' => 'mock')));
		
		// mock class to fetch the options passed to the constructor
		eval('class Db_Driver_Mock_Connection
		{
			protected $params;
			public function __construct()
				{ $this->params = func_get_args(); }
			public function getParams()
				{ return $this->params; }
		}');
		
		$c = Db::getConnection('foobar');
		
		$this->assertEquals(array('foobar', array('dbdriver' => 'mock')), $c->getParams());
		
		$this->assertSame($c, Db::getConnection('foobar'));
	}
	
	/**
	 * @covers Db::isChanged
	 */
	public function testIsChanged()
	{
		$this->initIsChangedTest();
		
		$obj = new stdClass();
		
		// empty __id returns true
		$this->assertTrue(Db::isChanged($obj));
		
		// id makes it return false if __data cannot be found
		$obj->__id = array('id' => 3);
		$this->assertFalse(Db::isChanged($obj));
		
		$obj->__data = array('ctitle' => 'Foo', 'cslug' => 'Bar');
		
		// we have a reference, so it should be modified
		$this->assertTrue(Db::isChanged($obj));
		
		// still modified after we've added one of the columns
		$obj->title = 'Foo';
		$this->assertTrue(Db::isChanged($obj));
		
		// we're back to full
		$obj->slug = 'Bar';
		$this->assertFalse(Db::isChanged($obj));
		
		// add additional
		$obj->someother = 'Something';
		$this->assertFalse(Db::isChanged($obj));
		
		$obj->title = 'foo';
		$this->assertTrue(Db::isChanged($obj));
		
		$obj->slug = 'Bar2';
		$this->assertTrue(Db::isChanged($obj));
	}
	/**
	 * @covers Db::isChanged
	 */
	public function testIsChangedProperty()
	{
		$this->initIsChangedTest();
		
		$obj = new stdClass();
		
		// empty __id returns true
		$this->assertTrue(Db::isChanged($obj, 'title'));
		
		// id makes it return false if __data cannot be found
		$obj->__id = array('id' => 3);
		$this->assertFalse(Db::isChanged($obj, 'title'));
		
		$obj->__data = array('ctitle' => 'Foo', 'cslug' => 'Bar');
		
		// we have a reference, so it should be modified
		$this->assertTrue(Db::isChanged($obj, 'title'));
		
		// still modified after we've added one of the columns
		$obj->title = 'Foo';
		$this->assertFalse(Db::isChanged($obj, 'title'));
		
		// we're back to full
		$obj->slug = 'Bar';
		$this->assertFalse(Db::isChanged($obj, 'title'));
		
		// add additional
		$obj->someother = 'Something';
		$this->assertFalse(Db::isChanged($obj, 'title'));
		
		$obj->title = 'foo';
		$this->assertTrue(Db::isChanged($obj, 'title'));
		
		$obj->slug = 'Bar2';
		$this->assertTrue(Db::isChanged($obj, 'title'));
		
		$this->assertFalse(Db::isChanged($obj, 'someother'));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Initializes a mocked mapper class for use with the isChanged() tests.
	 * 
	 * @return void
	 */
	public function initIsChangedTest()
	{
		if( ! class_exists('Db_Compiled_stdClassMapper'))
		{
			eval("class Db_Compiled_stdClassMapper
			{
				public \$properties = array(
					'title' => 'ctitle',
					'slug' => 'cslug'
					);
			}");
		}
	}
}


/* End of file DbTest.php */
/* Location: ./tests */