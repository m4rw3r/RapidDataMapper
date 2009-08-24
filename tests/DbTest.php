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
}


/* End of file DbTest.php */
/* Location: ./tests */