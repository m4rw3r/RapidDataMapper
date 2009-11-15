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
class Db_CodeBuilder_ClassTest extends PHPUnit_Framework_TestCase
{
	function setUp()
	{
		require_once dirname(__FILE__).'/../../../lib/Db/CodeBuilder/Container.php';
		require_once dirname(__FILE__).'/../../../lib/Db/CodeBuilder/Class.php';
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetName()
	{
		$c = new Db_CodeBuilder_Class();
		
		$this->assertEquals('class_', $c->getName());
		
		$c->name = 'Foo';
		
		$this->assertEquals('class_Foo', $c->getName());
	}
	
	// ------------------------------------------------------------------------
	
	public function testRender()
	{
		$c = new Db_CodeBuilder_Class();
		
		$this->assertEquals('class 
{
	
}', $c->__toString());
		
		$c->name = 'Foo';
		
		$this->assertEquals('class Foo
{
	
}', $c->__toString());
	}
	public function testRender2()
	{
		$c = new Db_CodeBuilder_Class();
		
		$c->name = 'Foo';
		$c->extends = 'Bar';
		
		$this->assertEquals('class Foo extends Bar
{
	
}', $c->__toString());
	}
	public function testRender3()
	{
		$c = new Db_CodeBuilder_Class();
		
		$c->name = 'Foo';
		$c->implements = 'Baz';
		
		$this->assertEquals('class Foo implements Baz
{
	
}', $c->__toString());
	}
	public function testRender4()
	{
		$c = new Db_CodeBuilder_Class();

		$c->name = 'Foo';
		$c->implements = 'Baz';
		$c->extends = 'Bar';

		$this->assertEquals('class Foo extends Bar implements Baz
{
	
}', $c->__toString());
	}
	public function testRender5()
	{
		$c = new Db_CodeBuilder_Class();
		
		$c->name = 'Foo';
		
		$this->assertTrue($c->addPart('line one'));
		
		$this->assertEquals('class Foo
{
	line one
}', $c->__toString());
		
		$this->assertTrue($c->addPart('line two'));
		
		$this->assertEquals('class Foo
{
	line one
	
	line two
}', $c->__toString());
	}
}


/* End of file ClassTest.php */
/* Location: ./tests/Db/CodeBuilder */