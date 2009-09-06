<?php
/*
 * Created by Martin Wernståhl on 2009-09-04.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../../lib/Db/Query.php';
require_once dirname(__FILE__).'/../../../lib/Db/Query/Update.php';
require_once dirname(__FILE__).'/../../../lib/Db/Exception.php';
require_once dirname(__FILE__).'/../../../lib/Db/Exception/QueryIncomplete.php';

/**
 * @covers Db_Query_Update
 * @runTestsInSeparateProcesses enabled
 * @preserveGlobalState disabled
 */
class Db_Query_UpdateTest extends PHPUnit_Framework_TestCase
{
	// ------------------------------------------------------------------------

	/**
	 * @expectedException Db_Exception_QueryIncomplete
	 */
	public function testNoData()
	{
		$q = new Db_Query_Update(new stdClass, 'table');
		
		$q->getSQL();
	}
	
	// ------------------------------------------------------------------------
	
	public function testSingleTable()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape'));
		
		
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(1))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('prefixed_table'))->will($this->returnValue('"prefixed_table"'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		
		$q = new Db_Query_Update($mock, 'table');
		
		$this->assertSame($q, $q->set('a', 'b'));
		$this->assertEquals('UPDATE "prefixed_table"
SET "a" = b_result', $q->getSQL());
	}
	public function testSingleTable2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape'));
		
		
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('d'))->will($this->returnValue('d_result'));
		$mock->expects($this->at(2))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('prefixed_table'))->will($this->returnValue('"prefixed_table"'));
		$mock->expects($this->at(4))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(5))->method('protectIdentifiers')->with($this->equalTo('c'))->will($this->returnValue('"c"'));
		
		$q = new Db_Query_Update($mock, 'table');
		
		$this->assertSame($q, $q->set('a', 'b'));
		$this->assertSame($q, $q->set('c', 'd'));
		$this->assertEquals('UPDATE "prefixed_table"
SET "a" = b_result, "c" = d_result', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testSingleTableNoEscape()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape'));
		
		$mock->expects($this->at(0))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('prefixed_table'))->will($this->returnValue('"prefixed_table"'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		
		$q = new Db_Query_Update($mock, 'table');
		
		$this->assertSame($q, $q->escape(false));
		$this->assertSame($q, $q->set('a', 'b'));
		$this->assertEquals('UPDATE "prefixed_table"
SET "a" = b', $q->getSQL());
	}
	public function testSingleTableNoEscape2()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape'));
		
		$mock->expects($this->at(0))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('prefixed_table'))->will($this->returnValue('"prefixed_table"'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('c'))->will($this->returnValue('"c"'));
		
		$q = new Db_Query_Update($mock, 'table');
		
		$this->assertSame($q, $q->escape(false));
		$this->assertSame($q, $q->set('a', 'b'));
		$this->assertSame($q, $q->set('c', 'd'));
		$this->assertEquals('UPDATE "prefixed_table"
SET "a" = b, "c" = d', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testSetSubquery()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape'));
		$q_mock = $this->getMock('Db_Query_Select', array('limit', '__toString'));
		
		$mock->expects($this->at(0))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('prefixed_table'))->will($this->returnValue('"prefixed_table"'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		
		$q_mock->expects($this->at(0))->method('limit')->with($this->equalTo(1));
		$q_mock->expects($this->at(1))->method('__toString')->will($this->returnValue('subquery_mock'));
		
		$q = new Db_Query_Update($mock, 'table');
		
		$this->assertSame($q, $q->set('a', $q_mock));
		$this->assertEquals('UPDATE "prefixed_table"
SET "a" = (subquery_mock)', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testLimit()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape', '_limit'));
		
		
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(1))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(2))->method('protectIdentifiers')->with($this->equalTo('prefixed_table'))->will($this->returnValue('"prefixed_table"'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(4))->method('_limit')->with('UPDATE "prefixed_table"
SET "a" = b_result')->will($this->returnValue('UPDATE "prefixed_table"
SET "a" = b_result
LIMIT 3'));
		
		$q = new Db_Query_Update($mock, 'table');
		
		$q->set('a', 'b');
		
		$this->assertSame($q, $q->limit(3));
		$this->assertEquals('UPDATE "prefixed_table"
SET "a" = b_result
LIMIT 3', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testOrderBy()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape'));
		
		
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(1))->method('protectIdentifiers')->with($this->equalTo('title'))->will($this->returnValue('"title"'));
		$mock->expects($this->at(2))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('prefixed_table'))->will($this->returnValue('"prefixed_table"'));
		$mock->expects($this->at(4))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		
		$q = new Db_Query_Update($mock, 'table');
		
		$q->set('a', 'b');
		
		$this->assertSame($q, $q->orderBy('title', 'desc'));
		$this->assertEquals('UPDATE "prefixed_table"
SET "a" = b_result
ORDER BY "title" DESC', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testWhere()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape'));
		
			$mock->expects($this->at(0))->method('protectIdentifiers')->with($this->equalTo('c'))->will($this->returnValue('"c"'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('d'))->will($this->returnValue('d_result'));
		$mock->expects($this->at(2))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(3))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(4))->method('protectIdentifiers')->with($this->equalTo('prefixed_table'))->will($this->returnValue('"prefixed_table"'));
		$mock->expects($this->at(5))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		
		$q = new Db_Query_Update($mock, 'table');
		
		$this->assertSame($q, $q->where('c', 'd'));
		$this->assertSame($q, $q->set('a', 'b'));
		$this->assertEquals('UPDATE "prefixed_table"
SET "a" = b_result
WHERE "c" = d_result', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testExecute()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape', 'query'));
		
		
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(1))->method('escape')->with($this->equalTo('d'))->will($this->returnValue('d_result'));
		$mock->expects($this->at(2))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('prefixed_table'))->will($this->returnValue('"prefixed_table"'));
		$mock->expects($this->at(4))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		$mock->expects($this->at(5))->method('protectIdentifiers')->with($this->equalTo('c'))->will($this->returnValue('"c"'));
		$mock->expects($this->at(6))->method('query')->with($this->equalTo('UPDATE "prefixed_table"
SET "a" = b_result, "c" = d_result'))->will($this->returnValue('db_result'));
		
		$q = new Db_Query_Update($mock, 'table');
		
		$q->set('a', 'b');
		$q->set('c', 'd');
		$this->assertEquals('db_result', $q->execute());
	}
	
	// ------------------------------------------------------------------------
	
	public function testMultipleTables()
	{
		$mock = $this->getMock('Db_Connection', array('protectIdentifiers', 'prefix', 'escape'));
		
		$mock->expects($this->at(0))->method('escape')->with($this->equalTo('b'))->will($this->returnValue('b_result'));
		$mock->expects($this->at(1))->method('prefix')->with($this->equalTo('table'))->will($this->returnValue('prefixed_table'));
		$mock->expects($this->at(2))->method('prefix')->with($this->equalTo('table2'))->will($this->returnValue('prefixed_table2'));
		$mock->expects($this->at(3))->method('protectIdentifiers')->with($this->equalTo('prefixed_table, prefixed_table2'))->will($this->returnValue('"prefixed_table", "prefixed_table2"'));
		$mock->expects($this->at(4))->method('protectIdentifiers')->with($this->equalTo('a'))->will($this->returnValue('"a"'));
		
		$q = new Db_Query_Update($mock, array('table', 'table2'));
		
		$q->set('a', 'b');
		$this->assertEquals('UPDATE "prefixed_table", "prefixed_table2"
SET "a" = b_result', $q->getSQL());
	}
}

/* End of file UpdateTest.php */
/* Location: ./tests/Db/Query */