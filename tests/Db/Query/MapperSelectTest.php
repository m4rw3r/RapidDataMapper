<?php
/*
 * Created by Martin Wernståhl on 2009-09-04.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Query_MapperSelect
 * @runTestsInSeparateProcesses enabled
 * @preserveGlobalState disabled
 */
class Db_Query_MapperSelectTest extends PHPUnit_Framework_TestCase
{
	// ------------------------------------------------------------------------
	
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../../lib/Db/Query.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Query/Select.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Query/MapperSelect.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception/QueryIncomplete.php';
	}
	
	// ------------------------------------------------------------------------
	
	public function testEmpty()
	{
		$this->markTestIncomplete('Mapper Select query tests.');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function testWherePrefixSuffix()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where_prefix = 'foo';
		$q->where_suffix = 'bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo bar', $q->getSQL());
	}
	public function testWherePrefixSuffix2()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where_prefix = 'foo(';
		$q->where_suffix = ')bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo bar', $q->getSQL());
	}
	public function testWherePrefixSuffix3()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where_prefix = 'foo( ';
		$q->where_suffix = ' )bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo bar', $q->getSQL());
	}
	public function testWherePrefixSuffix4()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where_prefix = 'foo (';
		$q->where_suffix = ') bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo bar', $q->getSQL());
	}
	public function testWherePrefixSuffix5()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where_prefix = 'foo AND (';
		$q->where_suffix = ') AND bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo bar', $q->getSQL());
	}
	public function testWherePrefixSuffix6()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where_prefix = 'foo  AND  (';
		$q->where_suffix = ')  AND  bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo bar', $q->getSQL());
	}
	public function testWherePrefixSuffix7()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where_prefix = 'fooAND (';
		$q->where_suffix = ') ANDbar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE fooAND ANDbar', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function testWherePrefixSuffixWWhere()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where = array('testing');
		$q->where_prefix = 'foo(';
		$q->where_suffix = ')bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo(testing)bar', $q->getSQL());
	}
	public function testWherePrefixSuffixWWhere2()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where = array('testing');
		$q->where_prefix = 'foo( ';
		$q->where_suffix = ' )bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo( testing )bar',$q->getSQL());
	}
	public function testWherePrefixSuffixWWhere3()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where = array('testing');
		$q->where_prefix = 'foo (';
		$q->where_suffix = ') bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo (testing) bar', $q->getSQL());
	}
	public function testWherePrefixSuffixWWhere4()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where = array('testing');
		$q->where_prefix = 'foo AND (';
		$q->where_suffix = ')';
		
		$this->assertEquals('SELECT *
FROM user
WHERE foo AND (testing)', $q->getSQL());
	}
	public function testWherePrefixSuffixWWhere5()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers', 'escape', 'prefix'));
		$mapper = $this->getMock('Db_Mapper', array('getConnection'));
		
		$mapper->expects($this->once())->method('getConnection')->will($this->returnValue($db));
		
		$q = new Db_Query_MapperSelect($mapper, 'object');
		
		$q->columns[] = '*';
		$q->from[] = 'user';
		$q->where = array('testing');
		$q->where_prefix = '(';
		$q->where_suffix = ') AND bar';
		
		$this->assertEquals('SELECT *
FROM user
WHERE (testing) AND bar', $q->getSQL());
	}
}

/* End of file MapperSelectTest.php */
/* Location: ./tests/Db/Query */