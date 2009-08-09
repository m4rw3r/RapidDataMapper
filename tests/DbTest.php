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
 * Tests the main Db object.
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
		
		$this->assertTrue($reflection->isAbstract());
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
}


/* End of file DbTest.php */
/* Location: ./tests */