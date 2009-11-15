<?php
/*
 * Created by Martin Wernståhl on 2009-11-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * 
 */
class Db_CodeBuilder_MethodTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../../lib/Db/CodeBuilder/Container.php';
		require_once dirname(__FILE__).'/../../../lib/Db/CodeBuilder/Method.php';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testCreateFail()
	{
		$m = new Db_CodeBuilder_Method();
	}
	public function testCreate()
	{
		$m = new Db_CodeBuilder_Method('something');
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetName()
	{
		$m = new Db_CodeBuilder_Method('some');
		
		$this->assertEquals('method_some', $m->getName());
	}
	public function testGetName2()
	{
		$m = new Db_CodeBuilder_Method('some', 'other');
		
		$this->assertEquals('method_some', $m->getName());
	}
	
	// ------------------------------------------------------------------------
	
	public function testRender()
	{
		$m = new Db_CodeBuilder_Method('some');
		
		$this->assertEquals('public function some()
{
	
}', $m->__toString());
	}
	public function testRender2()
	{
		$m = new Db_CodeBuilder_Method('some', '$param');
		
		$this->assertEquals('public function some($param)
{
	
}', $m->__toString());
	}
	public function testRender3()
	{
		$m = new Db_CodeBuilder_Method('some', '$param, $p2');
		
		$this->assertEquals('public function some($param, $p2)
{
	
}', $m->__toString());
	}
	public function testRender4()
	{
		$m = new Db_CodeBuilder_Method('some', '$param, $p = "foo"');
		
		$this->assertEquals('public function some($param, $p = "foo")
{
	
}', $m->__toString());
	}
	public function testRender5()
	{
		$m = new Db_CodeBuilder_Method('some', '$param, $p2');
		
		$this->assertTrue($m->addPart('line one'));
		
		$this->assertEquals('public function some($param, $p2)
{
	line one
}', $m->__toString());
	
	$this->assertTrue($m->addPart('line two'));
	
	$this->assertEquals('public function some($param, $p2)
{
	line one
	
	line two
}', $m->__toString());
	}
}


/* End of file MethodTest.php */
/* Location: ./tests/Db/CodeBuilder */