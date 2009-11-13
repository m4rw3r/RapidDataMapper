<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_CodeBuilder_Container
 */
class Db_CodeBuider_ContainerTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../../lib/Db/CodeBuilder/Container.php';
		
		if( ! class_exists('TestContainer', false))
		{
			eval('class TestContainer extends Db_CodeBuilder_Container
			{
				public function __construct($name = null)
					{ $this->setName($name); }
				protected $name;
				public function getName()
					{ return $this->name; }
				public function setName($name)
					{ $this->name = $name; }
			}');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function testIsAbstract()
	{
		$ref = new ReflectionClass('Db_CodeBuilder_Container');
		
		$this->assertTrue($ref->isAbstract(), 'Class is abstract');
		$this->assertTrue($ref->getMethod('getName') instanceof ReflectionMethod, 'Class has a method called getName()');
		$this->assertTrue($ref->getMethod('getName')->isAbstract(), 'The getName() method is abstract');
	}
	
	// ------------------------------------------------------------------------
	
	public function testSubclass()
	{
		$c = new TestContainer();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddPartArray()
	{
		$c = new TestContainer();
		
		$c->addPart(array());
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddPartInvalidClass()
	{
		$c = new TestContainer();
		
		$c->addPart(new stdClass);
	}
	
	// ------------------------------------------------------------------------
	
	public function testAddPartEmpty()
	{
		$c = new TestContainer();
		
		$c->addPart('');
		
		$this->assertSame('', $c->__toString());
		
		$c->addPart('');
		
		$this->assertSame('', $c->__toString());
	}
	
	// ------------------------------------------------------------------------
	
	public function testAddPart()
	{
		$c = new TestContainer();
		
		$c->addPart($n = new TestContainer());
		
		$this->assertSame(array($n), $c->getContent());
		
		$c->addPart($n2 = new TestContainer());
		
		$this->assertSame(array($n, $n2), $c->getContent());
		
		$c->addPart('foobar');
		
		$this->assertSame(array($n, $n2, 'foobar'), $c->getContent());
	}
	
	// ------------------------------------------------------------------------
	
	public function testAddPartReplace()
	{
		$c = new TestContainer();
		
		$this->assertTrue($c->addPart($n = new TestContainer('Foobar')));
		
		$this->assertSame(array(0 => $n), $c->getContent());
		
		$this->assertTrue($c->addPart($p = new TestContainer('Foobar'), '', true));
		
		$this->assertSame(array(0 => $p), $c->getContent());
		
		$this->assertFalse($c->addPart($k = new TestContainer('foo'), '', true));
		
		$this->assertSame(array(0 => $p), $c->getContent());
		
		$this->assertTrue($c->addPart($l = new TestContainer('foo')));
		
		$this->assertSame(array(0 => $p, 1 => $l), $c->getContent());
		
		$this->assertTrue($c->addPart($m = new TestContainer('Foobar'), '', true));
		
		$this->assertSame(array(0 => $m, 1 => $l), $c->getContent());
	}
	
	// ------------------------------------------------------------------------
	
	public function testAddPartReplace2()
	{
		$c = new TestContainer();
		
		$c->addPart('foo');
		
		$this->assertSame(array('foo'), $c->getContent());
		
		$this->assertFalse($c->addPart($o = new TestContainer(), '', true));
		
		$this->assertSame(array('foo'), $c->getContent());
		
		$this->assertTrue($c->addPart($d = new TestContainer('foo')));
		
		$this->assertSame(array('foo', $d), $c->getContent());
		
		$this->assertTrue($c->addPart($e = new TestContainer('foo'), '', true));
		
		$this->assertSame(array('foo', $e), $c->getContent());
	}
	
	// ------------------------------------------------------------------------
	
	public function testAddPartPath()
	{
		$c = new TestContainer();
		
		$c->addPart($d = new TestContainer('Foobar'));
		
		$this->assertTrue($c->addPart($e = new TestContainer('bar'), 'Foobar'));
		
		$this->assertContainsOnly($e, $d->getContent());
		
		$c->addPart($f = new TestContainer('baz'));
		
		$this->assertTrue($c->addPart($g = new TestContainer('bar'), 'baz'));
		
		$this->assertContainsOnly($g, $f->getContent());
		
		$this->assertSame(array($d, $f), $c->getContent());
	}
	
	// ------------------------------------------------------------------------
	
	public function testAddPartPathReplace()
	{
		$c = new TestContainer();
		
		$c->addPart($d = new TestContainer('Foobar'));
		
		$this->assertTrue($c->addPart($e = new TestContainer('bar'), 'Foobar'));
		
		$this->assertContainsOnly($e, $d->getContent());
		
		
		$this->assertTrue($c->addPart($g = new TestContainer('bar'), 'Foobar', true));
		
		$this->assertContainsOnly($g, $d->getContent());
		
		$this->assertContainsOnly($d, $c->getContent());
	}
	
	// ------------------------------------------------------------------------
	
	public function testRemovePart()
	{
		$c = new TestContainer();
		
		$c->addPart($d = new TestContainer('Foobar'));
		
		$d->addPart($e = new TestContainer('bar'));
		
		$d->addPart($f = new TestContainer('foo'));
		
		$c->addPart($g = new TestContainer('baz'));
		
		$g->addPart($h = new TestContainer('bar'));
		
		$this->assertTrue($c->removePart('Foobar.bar'));
		
		$this->assertContainsOnly($f, $d->getContent());
		
		$this->assertFalse($c->removePart('Foobar.bar'));
		$this->assertTrue($c->removePart('Foobar.foo'));
		
		$this->assertSame(array(), $d->getContent());
		
		$this->assertTrue($c->removePart('baz'));
		$this->assertSame(array($d), $c->getContent());
		
		$this->assertFalse($c->removePart('baz.bar'));
		$this->assertFalse($c->removePart('baz'));
		
		$this->assertTrue($c->removePart('Foobar'));
		$this->assertSame(array(), $c->getContent());
	}
	
	// ------------------------------------------------------------------------
	
	public function testGenerateGraph()
	{
		$c = new TestContainer();
		
		$this->assertEquals($c->generateGraph(), array());
		
		$c->addPart(new TestContainer());
		
		$this->assertEquals(array(''), $c->generateGraph());
		
		$p = new TestContainer();
		$p->setName('test');
		
		$c->addPart($p);
		
		$this->assertEquals(array('', 'test'), $c->generateGraph());
		
		$p->addPart(new TestContainer('foo'));
		
		$this->assertEquals(array('', 'test', 'test.foo'), $c->generateGraph());
		
		$c->addPart(new TestContainer('a'));
		
		$this->assertEquals(array('', 'test', 'test.foo', 'a'), $c->generateGraph());
		
		$c->addPart('Foobar');
		
		$this->assertEquals(array('', 'test', 'test.foo', 'a'), $c->generateGraph());
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
		$this->assertEquals(Db_CodeBuilder_Container::indentCode($code), $expect);
	}
}


/* End of file ContainerTest.php */
/* Location: ./tests/Db/CodeBuilder */