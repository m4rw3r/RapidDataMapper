<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../lib/Db.php';

Db::initAutoload();

/**
 * @covers Db
 * @runTestInSeparateProcess
 */
class DbTest extends PHPUnit_Framework_TestCase
{
	/**
	 * The Db class is a singleton, hence we need to restart PHP
	 * every time to make sure that it is reset
	 */
	public $runTestInSeparateProcess = true;
	
	
	// ------------------------------------------------------------------------

	/**
	 * Instantiation of Db class is not allowed.
	 */
	public function testClassInstantiation()
	{
		$reflection = new ReflectionClass('Db');
		
		$sum = false;
		
		$sum = ($sum OR $reflection->isAbstract());
		
		try
		{
			$c = $reflection->getConstructor();
			
			$sum = ($sum OR $c->isPrivate() OR $c->isProtected());
		}
		catch(RelfectionException $e)
		{
			var_dump($e);
		}
		
		$this->assertTrue($sum);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Test if exception is thrown on no configuration.
	 * 
	 * @expectedException Db_Exception_MissingConfig
	 */
	public function testNoConnection()
	{
		Db::getConnection();
	}
	/**
	 * @expectedException Db_Exception_MissingConfig
	 */
	public function testNoConnection2()
	{
		Db::setConnectionConfig('foobar', array('something' => 'to satisfy test'));
		
		Db::getConnection();
	}
	/**
	 * @expectedException Db_Exception_InvalidConfiguration
	 */
	public function testSetConnectionConfigInvalid()
	{
		Db::setConnectionConfig('testing');
	}
	/**
	 * @expectedException Db_Exception_InvalidConfiguration
	 */
	public function testSetConnectionConfigInvalid2()
	{
		Db::setConnectionConfig(null);
	}
	/**
	 * @expectedException Db_Exception_InvalidConfiguration
	 */
	public function testSetConnectionConfigInvalid3()
	{
		Db::setConnectionConfig('test', array());
	}
	
	public function testSetConnectionConfig()
	{
		Db::setConnectionConfig(array());
	}
	
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
	 * @group Foo
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
	 * Initializes a descriptor for use with the isChanged tests.
	 * 
	 * @return void
	 */
	public function initIsChangedTest()
	{
		if( ! class_exists('User_Descriptor'))
		{
			eval('class User_Descriptor extends Db_Descriptor
			{
				protected $builder;
				public function setBuilder($builder)
					{ $this->builder = $builder; }
				public function createBuilder()
					{ return $this->builder; }
			}');
		}
		
		$desc = new User_Descriptor();
		$desc->add($desc->newPrimaryKey('id'));
		$desc->setClass('stdClass');
		
		$desc->setBuilder("class Db_Compiled_stdClassMapper
		{
			public \$properties = array(
				'title' => 'ctitle',
				'slug' => 'cslug'
				);
		}");
		
		Db::addDescriptor($desc);
	}
}


/* End of file DbTest.php */
/* Location: ./tests */