<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../../lib/Db.php';

Db::initAutoload();

/**
 * Tests the main Db object.
 */
class Db_Mapper_CodeContainerTest extends PHPUnit_Framework_TestCase
{
	// ------------------------------------------------------------------------
	
	public function testIsAbstract()
	{
		$ref = new ReflectionClass('Db_Mapper_CodeContainer');
		
		$this->assertTrue($ref->isAbstract());
		$this->assertTrue($ref->getMethod('getName') instanceof ReflectionMethod);
		$this->assertTrue($ref->getMethod('getName')->isAbstract());
	}
	
	// ------------------------------------------------------------------------
	
	public function testSubclass()
	{
		eval('class AddContainer extends Db_Mapper_CodeContainer
		{
			protected $name;
			public function getName()
				{ return $this->name; }
			public function setName()
				{ $this->name = $name; }
		}');
		
		$c = new AddContainer();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException InvalidArgumentException
	 * @depends testSubclass
	 */
	public function testAddPart()
	{
		$c = new AddContainer();
		
		$c->addPart(array());
	}
	/**
	 * @expectedException InvalidArgumentException
	 * @depends testSubclass
	 */
	public function testAddPart2()
	{
		$c = new AddContainer();
		
		$c->addPart(new stdClass);
	}
	/**
	 * @depends testSubclass
	 */
	public function testAddPart3()
	{
		$c = new AddContainer();
		
		$c->addPart('');
		
		$this->assertSame($c->__toString(), '');
		
		$c->addPart('');
		
		$this->assertSame($c->__toString(), '');
		
		$this->markTestIncomplete('Add tests with paths and populated objects');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @depends testSubclass
	 */
	public function testRemovePart()
	{
		$this->markTestIncomplete('Add tests with paths and populated objects');
	}
	
	// ------------------------------------------------------------------------
	
	public function testIndentCode()
	{
		$this->checkIndent('foo', 'foo');
		$this->checkIndent('foo;', 'foo;');
		$this->checkIndent("foo\nbar", "foo\n\tbar");
		$this->checkIndent("\nfoo\nbar\n", "\n\tfoo\n\tbar\n\t");
		$this->checkIndent('// foo', '// foo');
		$this->checkIndent('
		test', '
			test');
		$this->checkIndent('foo = bar;
// test the comment', 'foo = bar;
	// test the comment');
		$this->checkIndent('/*foobar*/', '/*foobar*/');
		$this->checkIndent('// no strings:
echo "foobar
does not break";
test', '// no strings:
	echo "foobar
does not break";
	test');
		$this->checkIndent('// no strings:
echo \'foobar
does not break\';
test', '// no strings:
	echo \'foobar
does not break\';
	test');
		$this->checkIndent('// testing \' in comments:
echo "foo";', 		'// testing \' in comments:
	echo "foo";');
		$this->checkIndent('// testing \' in comments:
echo \'foo\';', 		'// testing \' in comments:
	echo \'foo\';');
		$this->checkIndent('// testing " in comments:
echo "foo";', 		'// testing " in comments:
	echo "foo";');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Asserts that the indentCode() method generates the correct data.
	 * 
	 * @param  string	Code to indent
	 * @param  string	Expected result
	 * @return void
	 */
	protected function checkIndent($code, $expect)
	{
		$this->assertEquals(Db_Mapper_CodeContainer::indentCode($code), $expect);
	}
}


/* End of file CodeContainer.php */
/* Location: ./tests/Db/Mapper */