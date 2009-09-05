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
	
	public function testEmpty()
	{
		$q = new Db_Query($this->getMock('Db_Connection'));
		
		$this->assertEquals((String) $q, '()');
	}
	
	// ------------------------------------------------------------------------
	
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
	
	public function testWhereNested()
	{
		$q = new Db_Query(new stdClass);
		
		$this->assertThat($q2 = $q->where(), $this->logicalNot($this->identicalTo($q)));
		$this->assertTrue($q2 instanceof Db_Query);
		
		$this->assertEquals('(())', (String) $q);
	}
	public function testWhereNested2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));	
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		
		$q = new Db_Query($mock);
		
		$q->where('a', 'b');
		$this->assertThat($q2 = $q->where(), $this->logicalNot($this->identicalTo($q)));
		$this->assertTrue($q2 instanceof Db_Query);
		
		$this->assertEquals('("a" = b_result AND ())', (String) $q);
	}
	public function testWhereNested3()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));	
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('c'))->will($this->returnValue('"c"'));	
		$mock->expects($this->at(3))->method('escape')->with($this->equalTo('d'))->will($this->returnValue('d_result'));
		
		$q = new Db_Query($mock);
		
		$q->where('a', 'b');
		$this->assertThat($q2 = $q->where(), $this->logicalNot($this->identicalTo($q)));
		$this->assertTrue($q2 instanceof Db_Query);
		$this->assertSame($q2, $q2->where('c', 'd'));
		
		$this->assertEquals('("a" = b_result AND ("c" = d_result))', (String) $q);
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
	
	// ------------------------------------------------------------------------
	
	public function testWhereOrNested()
	{
		$q = new Db_Query(new stdClass);
		
		$this->assertThat($q2 = $q->where('or'), $this->logicalNot($this->identicalTo($q)));
		$this->assertTrue($q2 instanceof Db_Query);
		
		$this->assertEquals('(())', (String) $q);
	}
	public function testWhereOrNested2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));	
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		
		$q = new Db_Query($mock);
		
		$q->where('a', 'b');
		$this->assertThat($q2 = $q->where('or'), $this->logicalNot($this->identicalTo($q)));
		$this->assertTrue($q2 instanceof Db_Query);
		
		$this->assertEquals('("a" = b_result OR ())', (String) $q);
	}
	public function testWhereOrNested3()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));	
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('c'))->will($this->returnValue('"c"'));	
		$mock->expects($this->at(3))->method('escape')->with($this->equalTo('d'))->will($this->returnValue('d_result'));
		
		$q = new Db_Query($mock);
		
		$q->where('a', 'b');
		$this->assertThat($q2 = $q->where('or'), $this->logicalNot($this->identicalTo($q)));
		$this->assertTrue($q2 instanceof Db_Query);
		$this->assertSame($q2, $q2->where('c', 'd'));
		
		$this->assertEquals('("a" = b_result OR ("c" = d_result))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereArray()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));	
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));	
		$mock->expects($this->at(3))->method('escape')->with($this->equalTo('c'))->will($this->returnValue('c_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where(array('a' => 'b')));
		$this->assertEquals('("a" = b_result)', (String) $q);
		$this->assertSame($q, $q->where(array('b' => 'c')));
		$this->assertEquals('("a" = b_result AND "b" = c_result)', (String) $q);
	}
	public function testWhereArray2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));	
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));	
		$mock->expects($this->at(3))->method('escape')->with($this->equalTo('c'))->will($this->returnValue('c_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where(array('a' => 'b', 'b' => 'c')));
		$this->assertEquals('("a" = b_result AND "b" = c_result)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereArrayNoKey()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));	
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('bc'))->will($this->returnValue('"bc"'));
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where(array('ab')));
		$this->assertEquals('("ab")', (String) $q);
		$this->assertSame($q, $q->where(array('bc')));
		$this->assertEquals('("ab" AND "bc")', (String) $q);
	}
	public function testWhereArrayNoKey2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));	
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('bc'))->will($this->returnValue('"bc"'));
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where(array('ab', 'bc')));
		$this->assertEquals('("ab" AND "bc")', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrWhereArray()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));	
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));	
		$mock->expects($this->at(3))->method('escape')->with($this->equalTo('c'))->will($this->returnValue('c_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where(array('or a' => 'b')));
		$this->assertEquals('("a" = b_result)', (String) $q);
		$this->assertSame($q, $q->where(array('or b' => 'c')));
		$this->assertEquals('("a" = b_result OR "b" = c_result)', (String) $q);
	}
	public function testOrWhereArray2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));	
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));	
		$mock->expects($this->at(3))->method('escape')->with($this->equalTo('c'))->will($this->returnValue('c_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where(array('or a' => 'b', 'or b' => 'c')));
		$this->assertEquals('("a" = b_result OR "b" = c_result)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrWhereArrayNoKey()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));	
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('bc'))->will($this->returnValue('"bc"'));
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where(array('or ab')));
		$this->assertEquals('("ab")', (String) $q);
		$this->assertSame($q, $q->where(array('or bc')));
		$this->assertEquals('("ab" OR "bc")', (String) $q);
	}
	public function testOrWhereArrayNoKey2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));	
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('bc'))->will($this->returnValue('"bc"'));
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->where(array('or ab', 'or bc')));
		$this->assertEquals('("ab" OR "bc")', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testBindWhere()
	{
		$mock = $this->getMock('Db_Connection', array('replaceBinds'));
		
		$mock->expects($this->once())->method('replaceBinds')->with($this->equalTo('a = ?'), $this->equalTo(array('foobar')))->will($this->returnValue('foobar_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->bindWhere('a = ?', array('foobar')));
		
		$this->assertEquals('(foobar_result)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testBindWhereMultiple()
	{
		$mock = $this->getMock('Db_Connection', array('replaceBinds'));
		
		$mock->expects($this->at(0))->method('replaceBinds')->with($this->equalTo('a = ?'), $this->equalTo(array('foobar')))->will($this->returnValue('foobar_result'));
		$mock->expects($this->at(1))->method('replaceBinds')->with($this->equalTo('b = ?'), $this->equalTo(array('bar', 'lol')))->will($this->returnValue('bar_lol'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->bindWhere('a = ?', array('foobar')));
		
		$this->assertEquals('(foobar_result)', (String) $q);
		
		$this->assertSame($q, $q->bindWhere('b = ?', array('bar', 'lol')));
		
		$this->assertEquals('(foobar_result AND bar_lol)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testBindWhereOr()
	{
		$mock = $this->getMock('Db_Connection', array('replaceBinds'));
		
		$mock->expects($this->once())->method('replaceBinds')->with($this->equalTo('a = ?'), $this->equalTo(array('foobar')))->will($this->returnValue('foobar_result'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->bindWhere('or a = ?', array('foobar')));
		
		$this->assertEquals('(foobar_result)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testBindWhereOrMultiple()
	{
		$mock = $this->getMock('Db_Connection', array('replaceBinds'));
		
		$mock->expects($this->at(0))->method('replaceBinds')->with($this->equalTo('a = ?'), $this->equalTo(array('foobar')))->will($this->returnValue('foobar_result'));
		$mock->expects($this->at(1))->method('replaceBinds')->with($this->equalTo('b = ?'), $this->equalTo(array('bar', 'lol')))->will($this->returnValue('bar_lol'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->bindWhere('or a = ?', array('foobar')));
		
		$this->assertEquals('(foobar_result)', (String) $q);
		
		$this->assertSame($q, $q->bindWhere('or b = ?', array('bar', 'lol')));
		
		$this->assertEquals('(foobar_result OR bar_lol)', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereIn()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->whereIn('a', array()));
		$this->assertEquals('("a" IN ())', (String) $q);
	}
	public function testWhereIn2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->whereIn('a', array('aa')));
		$this->assertEquals('("a" IN ("aa"))', (String) $q);
	}
	public function testWhereIn3()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		$mock->expects($this->at(2))->method('escape')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->whereIn('a', array('aa', 'ab')));
		$this->assertEquals('("a" IN ("aa", "ab"))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereInNoEscape()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->never())->method('protectIdentifiers');
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));
		
		$q = new Db_Query($mock);
		$q->escape(false);
		
		$this->assertSame($q, $q->whereIn('a', array('aa', 'ab')));
		$this->assertEquals('(a IN ("aa", "ab"))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereInOr()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		$mock->expects($this->at(2))->method('escape')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));
		$mock->expects($this->at(4))->method('escape')->with($this->equalTo('ba'))->will($this->returnValue('"ba"'));
		$mock->expects($this->at(5))->method('escape')->with($this->equalTo('bb'))->will($this->returnValue('"bb"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->whereIn('or a', array('aa', 'ab')));
		$this->assertEquals('("a" IN ("aa", "ab"))', (String) $q);
		$this->assertSame($q, $q->whereIn('or b', array('ba', 'bb')));
		$this->assertEquals('("a" IN ("aa", "ab") OR "b" IN ("ba", "bb"))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereInOrNoEscape()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->never())->method('protectIdentifiers');
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));
		$mock->expects($this->at(2))->method('escape')->with($this->equalTo('ba'))->will($this->returnValue('"ba"'));
		$mock->expects($this->at(3))->method('escape')->with($this->equalTo('bb'))->will($this->returnValue('"bb"'));
		
		$q = new Db_Query($mock);
		$q->escape(false);
		
		$this->assertSame($q, $q->whereIn('or a', array('aa', 'ab')));
		$this->assertEquals('(a IN ("aa", "ab"))', (String) $q);
		$this->assertSame($q, $q->whereIn('or b', array('ba', 'bb')));
		$this->assertEquals('(a IN ("aa", "ab") OR b IN ("ba", "bb"))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereNotIn()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->never())->method('escape');
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->whereNotIn('a', array()));
		$this->assertEquals('("a" NOT IN ())', (String) $q);
	}
	public function testWhereNotIn2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->whereNotIn('a', array('aa')));
		$this->assertEquals('("a" NOT IN ("aa"))', (String) $q);
	}
	public function testWhereNotIn3()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		$mock->expects($this->at(2))->method('escape')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->whereNotIn('a', array('aa', 'ab')));
		$this->assertEquals('("a" NOT IN ("aa", "ab"))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereNotInNoEscape()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->never())->method('protectIdentifiers');
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));
		
		$q = new Db_Query($mock);
		$q->escape(false);
		
		$this->assertSame($q, $q->whereNotIn('a', array('aa', 'ab')));
		$this->assertEquals('(a NOT IN ("aa", "ab"))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereNotInOr()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		$mock->expects($this->at(2))->method('escape')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));
		$mock->expects($this->at(4))->method('escape')->with($this->equalTo('ba'))->will($this->returnValue('"ba"'));
		$mock->expects($this->at(5))->method('escape')->with($this->equalTo('bb'))->will($this->returnValue('"bb"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->whereNotIn('or a', array('aa', 'ab')));
		$this->assertEquals('("a" NOT IN ("aa", "ab"))', (String) $q);
		$this->assertSame($q, $q->whereNotIn('or b', array('ba', 'bb')));
		$this->assertEquals('("a" NOT IN ("aa", "ab") OR "b" NOT IN ("ba", "bb"))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhereNotInOrNoEscape()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape'));
		
		$mock->expects($this->never())->method('protectIdentifiers');
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('aa'))->will($this->returnValue('"aa"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('ab'))->will($this->returnValue('"ab"'));
		$mock->expects($this->at(2))->method('escape')->with($this->equalTo('ba'))->will($this->returnValue('"ba"'));
		$mock->expects($this->at(3))->method('escape')->with($this->equalTo('bb'))->will($this->returnValue('"bb"'));
		
		$q = new Db_Query($mock);
		$q->escape(false);
		
		$this->assertSame($q, $q->whereNotIn('or a', array('aa', 'ab')));
		$this->assertEquals('(a NOT IN ("aa", "ab"))', (String) $q);
		$this->assertSame($q, $q->whereNotIn('or b', array('ba', 'bb')));
		$this->assertEquals('(a NOT IN ("aa", "ab") OR b NOT IN ("ba", "bb"))', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testLike()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escapeStr'));
		
		$mock->expects($this->once())->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->once())->method('escapeStr')->with($this->equalTo('aa'))->will($this->returnValue('aaaa'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->like('a', 'aa'));
		$this->assertEquals('("a" LIKE \'%aaaa%\')', (String) $q);
	}
	public function testLike2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escapeStr'));
		
		$mock->expects($this->at(0))->method('escapeStr')->with($this->equalTo('aa'))->will($this->returnValue('aaaa'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(2))->method('escapeStr')->with($this->equalTo('ba'))->will($this->returnValue('baba'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->like('a', 'aa'));
		$this->assertEquals('("a" LIKE \'%aaaa%\')', (String) $q);
		$this->assertSame($q, $q->like('b', 'ba'));
		$this->assertEquals('("a" LIKE \'%aaaa%\' AND "b" LIKE \'%baba%\')', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testLikeSides()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escapeStr'));
		
		$mock->expects($this->at(0))->method('escapeStr')->with($this->equalTo('aa'))->will($this->returnValue('aaaa'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(2))->method('escapeStr')->with($this->equalTo('ba'))->will($this->returnValue('baba'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));
		$mock->expects($this->at(4))->method('escapeStr')->with($this->equalTo('ca'))->will($this->returnValue('cccc'));
		$mock->expects($this->at(5))->method('protectIdentifiers')->with($this->equalTo('c'))->will($this->returnValue('"c"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->like('a', 'aa', 'both'));
		$this->assertEquals('("a" LIKE \'%aaaa%\')', (String) $q);
		$this->assertSame($q, $q->like('b', 'ba', 'left'));
		$this->assertEquals('("a" LIKE \'%aaaa%\' AND "b" LIKE \'%baba\')', (String) $q);
		$this->assertSame($q, $q->like('c', 'ca', 'right'));
		$this->assertEquals('("a" LIKE \'%aaaa%\' AND "b" LIKE \'%baba\' AND "c" LIKE \'cccc%\')', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testLikeNoEscape()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escapeStr'));
		
		$mock->expects($this->never())->method('protectIdentifiers');
		$mock->expects($this->at(0))->method('escapeStr')->with($this->equalTo('aa'))->will($this->returnValue('aaaa'));
		$mock->expects($this->at(1))->method('escapeStr')->with($this->equalTo('ba'))->will($this->returnValue('baba'));
		
		$q = new Db_Query($mock);
		$q->escape(false);
		
		$this->assertSame($q, $q->like('a', 'aa'));
		$this->assertEquals('(a LIKE \'%aaaa%\')', (String) $q);
		$this->assertSame($q, $q->like('b', 'ba'));
		$this->assertEquals('(a LIKE \'%aaaa%\' AND b LIKE \'%baba%\')', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrLike()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escapeStr'));
		
		$mock->expects($this->at(0))->method('escapeStr')->with($this->equalTo('aa'))->will($this->returnValue('aaaa'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(2))->method('escapeStr')->with($this->equalTo('ba'))->will($this->returnValue('baba'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('b'))->will($this->returnValue('"b"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->like('or a', 'aa'));
		$this->assertEquals('("a" LIKE \'%aaaa%\')', (String) $q);
		$this->assertSame($q, $q->like('or b', 'ba'));
		$this->assertEquals('("a" LIKE \'%aaaa%\' OR "b" LIKE \'%baba%\')', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrLikeNoEscape()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'escapeStr'));
		
		$mock->expects($this->never())->method('protectIdentifiers');
		$mock->expects($this->at(0))->method('escapeStr')->with($this->equalTo('aa'))->will($this->returnValue('aaaa'));
		$mock->expects($this->at(1))->method('escapeStr')->with($this->equalTo('ba'))->will($this->returnValue('baba'));
		
		$q = new Db_Query($mock);
		$q->escape(false);
		
		$this->assertSame($q, $q->like('or a', 'aa'));
		$this->assertEquals('(a LIKE \'%aaaa%\')', (String) $q);
		$this->assertSame($q, $q->like('or b', 'ba'));
		$this->assertEquals('(a LIKE \'%aaaa%\' OR b LIKE \'%baba%\')', (String) $q);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrderByNoCall()
	{
		$q = new Db_Query(new stdClass);
		
		$this->assertEquals(array(), $q->order_by);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrderBy()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$mock->expects($this->once())->method('protectIdentifiers')->with($this->equalTo('foobar'))->will($this->returnValue('"foobar"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->orderBy('foobar'));
		$this->assertEquals(array('"foobar"'), $q->order_by);
	}
	public function testOrderBy2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$mock->expects($this->once())->method('protectIdentifiers')->with($this->equalTo('foobar'))->will($this->returnValue('"foobar"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->orderBy('foobar', 'desc'));
		$this->assertEquals(array('"foobar" DESC'), $q->order_by);
	}
	public function testOrderBy3()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$mock->expects($this->once())->method('protectIdentifiers')->with($this->equalTo('foobar'))->will($this->returnValue('"foobar"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->orderBy('foobar', 'asc'));
		$this->assertEquals(array('"foobar" ASC'), $q->order_by);
	}
	public function testOrderBy4()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$mock->expects($this->once())->method('protectIdentifiers')->with($this->equalTo('foobar'))->will($this->returnValue('"foobar"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->orderBy('foobar', 'fjgfa'));
		$this->assertEquals(array('"foobar" ASC'), $q->order_by);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrderByRandom()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$mock->expects($this->never())->method('protectIdentifiers');
		
		$mock->RANDOM_KEYWORD = 'RANDOM_KEY';
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->orderBy('random'));
		$this->assertEquals(array('RANDOM_KEY'), $q->order_by);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrderByNoEscape()
	{
		$q = new Db_Query(new stdClass);
		$q->escape(false);
		
		$this->assertSame($q, $q->orderBy('SOMETHING TO NOT ESCAPE()'));
		$this->assertEquals(array('SOMETHING TO NOT ESCAPE()'), $q->order_by);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrderByList()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('foobar'))->will($this->returnValue('"foobar"'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('bar'))->will($this->returnValue('"bar"'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('baz'))->will($this->returnValue('"baz"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->orderBy('foobar, bar, baz', 'desc'));
		$this->assertEquals(array('"foobar" DESC', '"bar" DESC', '"baz" DESC'), $q->order_by);
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrderByArray()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('foobar'))->will($this->returnValue('"foobar"'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('bar'))->will($this->returnValue('"bar"'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('baz'))->will($this->returnValue('"baz"'));
		
		$q = new Db_Query($mock);
		
		$this->assertSame($q, $q->orderBy(array('foobar', 'bar', 'baz'), 'desc'));
		$this->assertEquals(array('"foobar" DESC', '"bar" DESC', '"baz" DESC'), $q->order_by);
	}
}


/* End of file QueryTest.php */
/* Location: ./tests/Db */