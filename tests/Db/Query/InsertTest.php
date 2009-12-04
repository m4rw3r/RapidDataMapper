<?php
/*
 * Created by Martin Wernståhl on 2009-09-04.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Query_Insert
 * @runTestsInSeparateProcesses enabled
 * @preserveGlobalState disabled
 */
class Db_Query_InsertTest extends PHPUnit_Framework_TestCase
{
	// ------------------------------------------------------------------------
	
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../../lib/Db/Query.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Query/Insert.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception.php';
		require_once dirname(__FILE__).'/../../../lib/Db/QueryBuilderException.php';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Db_QueryBuilderException
	 */
	public function testEmpty()
	{
		$i = new stdClass();
		
		$q = new Db_Query_Insert($i, 'test');
		
		$q->getSQL();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException Db_QueryBuilderException
	 */
	public function testNoValues()
	{
		$i = new stdClass();
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->add());
		
		$q->getSQL();
	}
	
	// ------------------------------------------------------------------------
	
	public function testSet()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('escape')->with('baz')->will($this->returnValue('escbaz'));
		$i->expects($this->at(1))->method('protectIdentifiers')->with('foobar')->will($this->returnValue('escfoobar'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->set('foobar', 'baz'));
		
		$this->assertSame('INSERT INTO esctable (escfoobar)
VALUES (escbaz)', $q->getSQL());
	}
	public function testSet2()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('escape')->with('baz')->will($this->returnValue('escbaz'));
		$i->expects($this->at(1))->method('escape')->with('baz2')->will($this->returnValue('escbaz2'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('foobar, foobar2')->will($this->returnValue('escfoobar, escfoobar2'));
		$i->expects($this->at(3))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->set('foobar', 'baz'));
		$this->assertSame($q, $q->set('foobar2', 'baz2'));
		
		$this->assertSame('INSERT INTO esctable (escfoobar, escfoobar2)
VALUES (escbaz, escbaz2)', $q->getSQL());
	}
	public function testSet3()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('protectIdentifiers')->with('foobar')->will($this->returnValue('escfoobar'));
		$i->expects($this->at(1))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$sq = $this->getMock('Db_Query_Select', array('__toString', 'limit'));
		
		$sq->expects($this->at(0))->method('limit')->with(1);
		$sq->expects($this->at(1))->method('__toString')->will($this->returnValue('generated_query'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->set('foobar', $sq));
		
		$this->assertSame('INSERT INTO esctable (escfoobar)
VALUES ((generated_query))', $q->getSQL());
	}
	public function testSet4()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('escape')->with('baz')->will($this->returnValue('escbaz'));
		$i->expects($this->at(1))->method('protectIdentifiers')->with('foobar, foobar2')->will($this->returnValue('escfoobar, escfoobar2'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$sq = $this->getMock('Db_Query_Select', array('__toString', 'limit'));
		
		$sq->expects($this->at(0))->method('limit')->with(1);
		$sq->expects($this->at(1))->method('__toString')->will($this->returnValue('generated_query'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->set('foobar', $sq));
		$this->assertSame($q, $q->set('foobar2', 'baz'));
		
		$this->assertSame('INSERT INTO esctable (escfoobar, escfoobar2)
VALUES ((generated_query), escbaz)', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testAdd()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('escape')->with('baz')->will($this->returnValue('escbaz'));
		$i->expects($this->at(1))->method('protectIdentifiers')->with('foobar')->will($this->returnValue('escfoobar'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->add());
		$this->assertSame($q, $q->set('foobar', 'baz'));
		
		$this->assertSame('INSERT INTO esctable (escfoobar)
VALUES (escbaz)', $q->getSQL());
	}
	public function testAdd2()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('escape')->with('baz')->will($this->returnValue('escbaz'));
		$i->expects($this->at(1))->method('escape')->with('baz2')->will($this->returnValue('escbaz2'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('foobar, foobar2')->will($this->returnValue('escfoobar, escfoobar2'));
		$i->expects($this->at(3))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->add(array('foobar' => 'baz')));
		$this->assertSame($q, $q->set('foobar2', 'baz2'));
		
		$this->assertSame('INSERT INTO esctable (escfoobar, escfoobar2)
VALUES (escbaz, escbaz2)', $q->getSQL());
	}
	public function testAdd3()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('escape')->with('baz')->will($this->returnValue('escbaz'));
		$i->expects($this->at(1))->method('escape')->with('baz2')->will($this->returnValue('escbaz2'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('foobar')->will($this->returnValue('escfoobar'));
		$i->expects($this->at(3))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->set(array('foobar' => 'baz')));
		$this->assertSame($q, $q->add(array('foobar' => 'baz2')));
		
		$this->assertSame('INSERT INTO esctable (escfoobar)
VALUES (escbaz), (escbaz2)', $q->getSQL());
	}
	public function testAdd4()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('escape')->with('baz')->will($this->returnValue('escbaz'));
		$i->expects($this->at(1))->method('escape')->with('baz2')->will($this->returnValue('escbaz2'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('foobar, foobar2')->will($this->returnValue('escfoobar, escfoobar2'));
		$i->expects($this->at(3))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->set(array('foobar' => 'baz')));
		$this->assertSame($q, $q->add(array('foobar2' => 'baz2')));
		
		$this->assertSame('INSERT INTO esctable (escfoobar, escfoobar2)
VALUES (escbaz, NULL), (NULL, escbaz2)', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testNoEscape()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('protectIdentifiers')->with('foobar')->will($this->returnValue('escfoobar'));
		$i->expects($this->at(1))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->escape(false));
		$this->assertSame($q, $q->set('foobar', 'baz'));
		
		$this->assertSame('INSERT INTO esctable (escfoobar)
VALUES (baz)', $q->getSQL());
	}
	public function testNoEscape2()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = '';
		
		$i->expects($this->at(0))->method('escape')->with('baz2')->will($this->returnValue('escbaz2'));
		$i->expects($this->at(1))->method('protectIdentifiers')->with('foobar, foobar2')->will($this->returnValue('escfoobar, escfoobar2'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('test')->will($this->returnValue('esctable'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->escape(false));
		$this->assertSame($q, $q->set('foobar', 'baz'));
		$this->assertSame($q, $q->escape(true));
		$this->assertSame($q, $q->set('foobar2', 'baz2'));
		
		$this->assertSame('INSERT INTO esctable (escfoobar, escfoobar2)
VALUES (baz, escbaz2)', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testSetWBdprefix()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers'));
		$i->dbprefix = 'foobar_';
		
		$i->expects($this->at(0))->method('escape')->with('baz')->will($this->returnValue('escbaz'));
		$i->expects($this->at(1))->method('protectIdentifiers')->with('foobar')->will($this->returnValue('escfoobar'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('foobar_test')->will($this->returnValue('esctable'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->set('foobar', 'baz'));
		
		$this->assertSame('INSERT INTO esctable (escfoobar)
VALUES (escbaz)', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testExecute()
	{
		$i = $this->getMock('Db_Connection', array('escape', 'protectIdentifiers', 'query'));
		$i->dbprefix = 'foobar_';
		
		$i->expects($this->at(0))->method('escape')->with('baz')->will($this->returnValue('escbaz'));
		$i->expects($this->at(1))->method('protectIdentifiers')->with('foobar')->will($this->returnValue('escfoobar'));
		$i->expects($this->at(2))->method('protectIdentifiers')->with('foobar_test')->will($this->returnValue('esctable'));
		$i->expects($this->at(3))->method('query')->with('INSERT INTO esctable (escfoobar)
VALUES (escbaz)')->will($this->returnValue('executed'));
		
		$q = new Db_Query_Insert($i, 'test');
		
		$this->assertSame($q, $q->set('foobar', 'baz'));
		
		$this->assertSame('executed', $q->execute());
	}
}

/* End of file InsertTest.php */
/* Location: ./tests/Db/Query */