<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Query
 * @runTestsInSeparateProcesses enabled	
 * @preserveGlobalState disabled
 */
class Db_QueryTest extends PHPUnit_Framework_TestCase
{
	// ------------------------------------------------------------------------
	
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../lib/Db/Query.php';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 
	 */
	public function testEmpty()
	{
		$q = new Db_Query($this->getMock('Db_Connection'));
		
		$this->assertEquals((String) $q, '()');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @runInSeparateProcess
	 */
	public function testParent()
	{
		$q = new Db_Query(new stdClass, $p = new stdClass());
		
		$this->assertSame($p, $q->end());
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhere()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->exactly(2))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('a_result'));
		$mock->expects($this->exactly(2))->method('escape')->with($this->equalTo('foobar'))->will($this->returnValue('foobar_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where('a', 'foobar'));
		$this->assertEquals('(a_result = foobar_result)', (String) $q);
		
		$this->assertSame($q, $q->where('a', 'foobar'));
		$this->assertEquals('(a_result = foobar_result AND a_result = foobar_result)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereNoEscape()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->once())->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('a_result'));
		$mock->expects($this->once())->method('escape')->with($this->equalTo('foobar'))->will($this->returnValue('foobar_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->escape(false));
		$this->assertSame($q, $q->where('a', 'foobar'));
		$this->assertSame($q, $q->escape(true));
		$this->assertSame($q, $q->where('a', 'foobar'));
		
		$this->assertEquals('(a =foobar AND a_result = foobar_result)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereAddCondition()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a = b'))->will($this->returnValue('"a" = "b"'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('aa > cc'))->will($this->returnValue('"aa" > "cc"'));
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where('a = b'));
		
		$this->assertEquals('("a" = "b")', (String) $q);
		
		$this->assertSame($q, $q->where('aa > cc'));
		
		$this->assertEquals('("a" = "b" AND "aa" > "cc")', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereAddRawCondition()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->never())->method('protectIdentifiers');
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->escape(false));
		$this->assertSame($q, $q->where('a = b foo'));
		
		$this->assertEquals('(a = b foo)', (String) $q);
		
		$this->assertSame($q, $q->where('a = b bar'));
		
		$this->assertEquals('(a = b foo AND a = b bar)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereWithBind()
	{
		$mock = $this->getMock('Db_Connection', array('replaceBinds'));
		
		$mock->expects($this->once())->method('replaceBinds')->with($this->equalTo('a = ?'), $this->equalTo(array('foobar')))->will($this->returnValue('foobar_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where('a = ?', array('foobar')));
		
		$this->assertEquals('(foobar_result)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereWithBindMultiple()
	{
		$mock = $this->getMock('Db_Connection', array('replaceBinds'));
		
		$mock->expects($this->at(0))->method('replaceBinds')->with($this->equalTo('a = ?'), $this->equalTo(array('foobar')))->will($this->returnValue('foobar_result'));
		$mock->expects($this->at(1))->method('replaceBinds')->with($this->equalTo('b = ?'), $this->equalTo(array('bar', 'lol')))->will($this->returnValue('bar_lol'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where('a = ?', array('foobar')));
		
		$this->assertEquals('(foobar_result)', (String) $q);
		
		$this->assertSame($q, $q->where('b = ?', array('bar', 'lol')));
		
		$this->assertEquals('(foobar_result AND bar_lol)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereWithSubquery()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		$sub = $this->getMock('Db_Query_Select', array('__toString'));
		$sub2 = $this->getMock('Db_Query_Select', array('__toString'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));
		
		$sub->expects($this->once())->method('__toString')->will($this->returnValue('subquery_a'));
		$sub2->expects($this->once())->method('__toString')->will($this->returnValue('subquery_b'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where('a', $sub));
		$this->assertEquals('("a" = (subquery_a))', (String) $q);
		$this->assertSame($q, $q->where('b', $sub2));
		$this->assertEquals('("a" = (subquery_a) AND "b" = (subquery_b))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereOr()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('a_id'));	
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('foobar'))->will($this->returnValue('foobar_result'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('b_id'));
		$mock->expects($this->at(3))->method('escape')->with($this->equalTo('bar'))->will($this->returnValue('bar_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where('or a', 'foobar'));
		$this->assertEquals('(a_id = foobar_result)', (String) $q);
		$this->assertSame($q, $q->where('or b', 'bar'));
		$this->assertEquals('(a_id = foobar_result OR b_id = bar_result)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereOrNoEscape()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->never())->method('protectIdentifiers');
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$q->escape(false);
		$this->assertSame($q, $q->where('or a', 'foobar'));
		$this->assertEquals('(a =foobar)', (String) $q);
		$this->assertSame($q, $q->where('or b', 'bar'));
		$this->assertEquals('(a =foobar OR b =bar)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereOrAddCondition()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a = b'))->will($this->returnValue('"a" = "b"'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('aa > cc'))->will($this->returnValue('"aa" > "cc"'));
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where('or a = b'));
		
		$this->assertEquals('("a" = "b")', (String) $q);
		
		$this->assertSame($q, $q->where('or aa > cc'));
		
		$this->assertEquals('("a" = "b" OR "aa" > "cc")', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereOrWithBind()
	{
		$mock = $this->getMock('Db_Connection', array('replaceBinds'));
		
		$mock->expects($this->at(0))->method('replaceBinds')->with($this->equalTo('a = ?'), $this->equalTo(array('foobar')))->will($this->returnValue('foobar_result'));
		$mock->expects($this->at(1))->method('replaceBinds')->with($this->equalTo('b = ?'), $this->equalTo(array('bar', 'lol')))->will($this->returnValue('bar_lol'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where('or a = ?', array('foobar')));
		
		$this->assertEquals('(foobar_result)', (String) $q);
		
		$this->assertSame($q, $q->where('or b = ?', array('bar', 'lol')));
		
		$this->assertEquals('(foobar_result OR bar_lol)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereOrWithSubquery()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		$sub = $this->getMock('Db_Query_Select', array('__toString'));
		$sub2 = $this->getMock('Db_Query_Select', array('__toString'));
		
			$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
			$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));
		
		$sub->expects($this->once())->method('__toString')->will($this->returnValue('subquery_a'));
		$sub2->expects($this->once())->method('__toString')->will($this->returnValue('subquery_b'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where('or a', $sub));
		$this->assertEquals('("a" = (subquery_a))', (String) $q);
		$this->assertSame($q, $q->where('or b', $sub2));
		$this->assertEquals('("a" = (subquery_a) OR "b" = (subquery_b))', (String) $q);
	}
}


/* End of file QueryTest.php */
/* Location: ./tests/Db */