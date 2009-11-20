<?php
/*
 * Created by Martin Wernståhl on 2009-09-04.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Query_Select
 * @runTestsInSeparateProcesses enabled
 * @preserveGlobalState disabled
 */
class Db_Query_SelectTest extends PHPUnit_Framework_TestCase
{
	// ------------------------------------------------------------------------
	
	public function setUp()
	{
		
		require_once dirname(__FILE__).'/../../../lib/Db/Exception.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception/QueryIncomplete.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Query.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Query/Select.php';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testEmpty()
	{
		$q = new Db_Query_Select();
	}
	/**
	 * @expectedException Db_Exception_QueryIncomplete
	 */
	public function testEmpty2()
	{
		$q = new Db_Query_Select(new stdClass());
		
		$q->getSQL();
	}
	
	// ------------------------------------------------------------------------
	
	public function testEnd()
	{
		$q = new Db_Query_Select(new stdClass(), $i = new stdClass());
		
		$this->assertSame($i, $q->end());
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetSQLCallsToString()
	{
		$q = $this->getMock('Db_Query_Select', array('__toString'), array(new stdClass()));
		
		$q->expects($this->once())->method('__toString')->will($this->returnValue('this is a test'));
		
		$this->assertEquals('this is a test', $q->getSQL());
	}
	
	// ------------------------------------------------------------------------
	
	public function testGetCallsToStringAndPassesToDb()
	{
		$db = $this->getMock('Db_Connection', array('query'));
		
		$db->expects($this->once())->method('query')->with('this is a test')->will($this->returnValue('this should be a test'));
		
		$q = $this->getMock('Db_Query_Select', array('__toString'), array($db));
		
		$q->expects($this->once())->method('__toString')->will($this->returnValue('this is a test'));
		
		$this->assertEquals('this should be a test', $q->get());
	}
	
	// ------------------------------------------------------------------------
	
	public function testDistinct()
	{
		$q = new Db_Query_Select(new stdClass());
		
		$this->assertFalse($q->distinct);
		
		$this->assertSame($q, $q->distinct(true));
		
		$this->assertTrue($q->distinct);
		
		$this->assertSame($q, $q->distinct(false));
		
		$this->assertFalse($q->distinct);
	}
	
	// ------------------------------------------------------------------------
	
	public function testColumn()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('column')->will($this->returnValue('esccol'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('col')->will($this->returnValue('escc'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column('column'));
		
		$this->assertEquals(array('esccol'), $q->columns);
		
		$this->assertSame($q, $q->column('col'));
		
		$this->assertEquals(array('esccol', 'escc'), $q->columns);
	}
	public function testColumn2()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('column')->will($this->returnValue('esccol'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('alias')->will($this->returnValue('escalias'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('col')->will($this->returnValue('escc'));
		$db->expects($this->at(3))->method('protectIdentifiers')->with('lolcat')->will($this->returnValue('elol'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column(array('alias' => 'column')));
		
		$this->assertEquals(array('esccol AS escalias'), $q->columns);
		
		$this->assertSame($q, $q->column(array('lolcat' => 'col')));
		
		$this->assertEquals(array('esccol AS escalias', 'escc AS elol'), $q->columns);
	}
	public function testColumn3()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->expects($this->never())->method('protectIdentifiers');
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->escape(false));
		
		$this->assertSame($q, $q->column('column'));
		
		$this->assertEquals(array('column'), $q->columns);
		
		$this->assertSame($q, $q->column('col'));
		
		$this->assertEquals(array('column', 'col'), $q->columns);
	}
	public function testColumn4()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('alias')->will($this->returnValue('escalias'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('lolcat')->will($this->returnValue('elol'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->escape(false));
		
		$this->assertSame($q, $q->column(array('alias' => 'column')));
		
		$this->assertEquals(array('column AS escalias'), $q->columns);
		
		$this->assertSame($q, $q->column(array('lolcat' => 'col')));
		
		$this->assertEquals(array('column AS escalias', 'col AS elol'), $q->columns);
	}
	public function testColumn5()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('column, col2')->will($this->returnValue('escalias'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('c2, cl2')->will($this->returnValue('ecalias'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column('column, col2'));
		
		$this->assertEquals(array('escalias'), $q->columns);
		
		$this->assertSame($q, $q->column('c2, cl2'));
		
		$this->assertEquals(array('escalias', 'ecalias'), $q->columns);
	}
	public function testColumn6()
	{
		$sq = $this->getMock('Db_Query_Select', array('__toString'), array(new stdClass));
		
		$sq->expects($this->once())->method('__toString')->will($this->returnValue('subquery'));
		
		$q = new Db_Query_Select(new stdClass);
		
		$this->assertSame($q, $q->column($sq));
		
		$this->assertEquals(array('(subquery)'), $q->columns);
	}
	public function testColumn7()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('foobar')->will($this->returnValue('escfoo'));
		
		$sq = $this->getMock('Db_Query_Select', array('__toString'), array(new stdClass));
		
		$sq->expects($this->once())->method('__toString')->will($this->returnValue('subquery'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column(array('foobar' => $sq)));
		
		$this->assertEquals(array('(subquery) AS escfoo'), $q->columns);
	}
	
	// ------------------------------------------------------------------------
	
	public function testColumnWithTableAndAutoPrefix()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('ThePrefixTest.column')->will($this->returnValue('esccol'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('ThePrefixTest2.col')->will($this->returnValue('escc'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column('column', 'Test'));
		
		$this->assertEquals(array('esccol'), $q->columns);
		
		$this->assertSame($q, $q->column('col', 'Test2'));
		
		$this->assertEquals(array('esccol', 'escc'), $q->columns);
	}
	public function testColumnWithTableAndAutoPrefix2()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('ThePrefixTest.column')->will($this->returnValue('esccol'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('alias')->will($this->returnValue('escalias'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('ThePrefixTest2.col')->will($this->returnValue('escc'));
		$db->expects($this->at(3))->method('protectIdentifiers')->with('lolcat')->will($this->returnValue('elol'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column(array('alias' => 'column'), 'Test'));
		
		$this->assertEquals(array('esccol AS escalias'), $q->columns);
		
		$this->assertSame($q, $q->column(array('lolcat' => 'col'), 'Test2'));
		
		$this->assertEquals(array('esccol AS escalias', 'escc AS elol'), $q->columns);
	}
	public function testColumnWithTableAndAutoPrefix3()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('ThePrefixTest.column')->will($this->returnValue('esccol'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('ThePrefixTest.col2')->will($this->returnValue('esccol2'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('ThePrefixTest2.col')->will($this->returnValue('escc'));
		$db->expects($this->at(3))->method('protectIdentifiers')->with('ThePrefixTest2.co')->will($this->returnValue('esc'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column('column, col2', 'Test'));
		
		$this->assertEquals(array('esccol', 'esccol2'), $q->columns);
		
		$this->assertSame($q, $q->column('col, co', 'Test2'));
		
		$this->assertEquals(array('esccol', 'esccol2', 'escc', 'esc'), $q->columns);
	}
	
	// ------------------------------------------------------------------------
	
	public function testColumnWithTableAndNoPrefix()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('Test.column')->will($this->returnValue('esccol'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('Test2.col')->will($this->returnValue('escc'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column('column', 'Test', true));
		
		$this->assertEquals(array('esccol'), $q->columns);
		
		$this->assertSame($q, $q->column('col', 'Test2', true));
		
		$this->assertEquals(array('esccol', 'escc'), $q->columns);
	}
	public function testColumnWithTableAndNoPrefixs2()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('Test.column')->will($this->returnValue('esccol'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('alias')->will($this->returnValue('escalias'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('Test2.col')->will($this->returnValue('escc'));
		$db->expects($this->at(3))->method('protectIdentifiers')->with('lolcat')->will($this->returnValue('elol'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column(array('alias' => 'column'), 'Test', true));
		
		$this->assertEquals(array('esccol AS escalias'), $q->columns);
		
		$this->assertSame($q, $q->column(array('lolcat' => 'col'), 'Test2', true));
		
		$this->assertEquals(array('esccol AS escalias', 'escc AS elol'), $q->columns);
	}
	public function testColumnWithTableAndNoPrefix3()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('Test.column')->will($this->returnValue('esccol'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('Test.col2')->will($this->returnValue('esccol2'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('Test2.col')->will($this->returnValue('escc'));
		$db->expects($this->at(3))->method('protectIdentifiers')->with('Test2.co')->will($this->returnValue('esc'));
		
		$q = new Db_Query_Select($db);
		
		$this->assertSame($q, $q->column('column, col2', 'Test', true));
		
		$this->assertEquals(array('esccol', 'esccol2'), $q->columns);
		
		$this->assertSame($q, $q->column('col, co', 'Test2', true));
		
		$this->assertEquals(array('esccol', 'esccol2', 'escc', 'esc'), $q->columns);
	}
	
	// ------------------------------------------------------------------------
	
	public function testFrom()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('ThePrefixTable')->will($this->returnValue('esct'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('t1')->will($this->returnValue('e'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('t1.*')->will($this->returnValue('cols'));
		$db->expects($this->at(3))->method('protectIdentifiers')->with('ThePrefixTable2')->will($this->returnValue('esc'));
		$db->expects($this->at(4))->method('protectIdentifiers')->with('t2')->will($this->returnValue('e2'));
		$db->expects($this->at(5))->method('protectIdentifiers')->with('t2.*')->will($this->returnValue('cols2'));
		
		$q = $this->getMock('Db_Query_Select', array('column'), array($db));
		
		$q->expects($this->never())->method('column');
		
		$this->assertSame($q, $q->from('Table'));
		
		$this->assertEquals(array('esct AS e'), $q->from);
		$this->assertEquals(array('cols'), $q->columns);
		
		$this->assertSame($q, $q->from('Table2'));
		
		$this->assertEquals(array('esct AS e', 'esc AS e2'), $q->from);
		$this->assertEquals(array('cols', 'cols2'), $q->columns);
	}
	public function testFrom2()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('ThePrefixTable')->will($this->returnValue('esct'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('t1')->will($this->returnValue('e'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('ThePrefixTable2')->will($this->returnValue('esc'));
		$db->expects($this->at(3))->method('protectIdentifiers')->with('t2')->will($this->returnValue('e2'));
		
		$q = $this->getMock('Db_Query_Select', array('column'), array($db));
		
		$q->expects($this->at(0))->method('column')->with('column1', 't1');
		$q->expects($this->at(1))->method('column')->with('col2', 't2');
		
		$this->assertSame($q, $q->from('Table', 'column1'));
		
		$this->assertEquals(array('esct AS e'), $q->from);
		
		$this->assertSame($q, $q->from('Table2', 'col2'));
		
		$this->assertEquals(array('esct AS e', 'esc AS e2'), $q->from);
	}
	public function testFrom3()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('t1')->will($this->returnValue('e'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('t1.*')->will($this->returnValue('cols'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('t2')->will($this->returnValue('e2'));
		$db->expects($this->at(3))->method('protectIdentifiers')->with('t2.*')->will($this->returnValue('cols2'));
		
		$sq1 = $this->getMock('Db_Query_Select', array('__toString'), array(new stdClass));
		$sq2 = $this->getMock('Db_Query_Select', array('__toString'), array(new stdClass));
		
		$sq1->expects($this->once())->method('__toString')->will($this->returnValue('sq1'));
		$sq2->expects($this->once())->method('__toString')->will($this->returnValue('sq2'));
		
		$q = $this->getMock('Db_Query_Select', array('column'), array($db));
		
		$q->expects($this->never())->method('column');
		
		$this->assertSame($q, $q->from($sq1));
		
		$this->assertEquals(array('(sq1) AS e'), $q->from);
		$this->assertEquals(array('cols'), $q->columns);
		
		$this->assertSame($q, $q->from($sq2));
		
		$this->assertEquals(array('(sq1) AS e', '(sq2) AS e2'), $q->from);
		$this->assertEquals(array('cols', 'cols2'), $q->columns);
	}
	public function testFrom4()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'ThePrefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('s1')->will($this->returnValue('e'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('s1.*')->will($this->returnValue('cols'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('s2')->will($this->returnValue('e2'));
		$db->expects($this->at(3))->method('protectIdentifiers')->with('s2.*')->will($this->returnValue('cols2'));
		
		$sq1 = $this->getMock('Db_Query_Select', array('__toString'), array(new stdClass));
		$sq2 = $this->getMock('Db_Query_Select', array('__toString'), array(new stdClass));
		
		$sq1->expects($this->once())->method('__toString')->will($this->returnValue('sq1'));
		$sq2->expects($this->once())->method('__toString')->will($this->returnValue('sq2'));
		
		$q = $this->getMock('Db_Query_Select', array('column'), array($db));
		
		$q->expects($this->never())->method('column');
		
		$this->assertSame($q, $q->from(array('s1' => $sq1)));
		
		$this->assertEquals(array('(sq1) AS e'), $q->from);
		$this->assertEquals(array('cols'), $q->columns);
		
		$this->assertSame($q, $q->from(array('s2' => $sq2)));
		
		$this->assertEquals(array('(sq1) AS e', '(sq2) AS e2'), $q->from);
		$this->assertEquals(array('cols', 'cols2'), $q->columns);
	}
	
	// ------------------------------------------------------------------------
	
	public function testJoin()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'Prefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('PrefixTable')->will($this->returnValue('esct'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('t1')->will($this->returnValue('et1'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('t1.*')->will($this->returnValue('ecols'));
		
		$q = $this->getMock('Db_Query_Select', array('createCondition', 'column'), array($db));
		
		$q->expects($this->once())->method('createCondition')->with($this->equalTo('foobar'), $this->isNull(), $this->equalTo(array()))->will($this->returnCallback(array(__CLASS__, 'dummyCreateCondition')));
		
		$this->assertSame($q, $q->join('Table', 'foobar'));
		
		$this->assertEquals(array('LEFT JOIN esct AS et1 ON foobar$'), $q->join);
		$this->assertEquals(array('ecols'), $q->columns);
	}
	public function testJoin2()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'Prefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('PrefixTable')->will($this->returnValue('esct'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('al')->will($this->returnValue('escal'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('al.*')->will($this->returnValue('ecols'));
		
		$q = $this->getMock('Db_Query_Select', array('createCondition', 'column'), array($db));
		
		$q->expects($this->once())->method('createCondition')->with($this->equalTo('foobar'), $this->isNull(), $this->equalTo(array()))->will($this->returnCallback(array(__CLASS__, 'dummyCreateCondition')));
		
		$this->assertSame($q, $q->join(array('al' => 'Table'), 'foobar'));
		
		$this->assertEquals(array('LEFT JOIN esct AS escal ON foobar$'), $q->join);
		$this->assertEquals(array('ecols'), $q->columns);
	}
	public function testJoin3()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'Prefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('PrefixTable')->will($this->returnValue('esct'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('t1')->will($this->returnValue('et1'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('t1.*')->will($this->returnValue('ecols'));
		
		$q = $this->getMock('Db_Query_Select', array('createCondition', 'column'), array($db));
		
		$q->expects($this->once())->method('createCondition')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(array()))->will($this->returnCallback(array(__CLASS__, 'dummyCreateCondition')));
		
		$this->assertSame($q, $q->join('Table', array('foo' => 'bar')));
		
		$this->assertEquals(array('LEFT JOIN esct AS et1 ON foo$bar'), $q->join);
		$this->assertEquals(array('ecols'), $q->columns);
	}
	public function testJoin4()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'Prefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('PrefixTable')->will($this->returnValue('esct'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('t1')->will($this->returnValue('et1'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('t1.*')->will($this->returnValue('ecols'));
		
		$q = $this->getMock('Db_Query_Select', array('createCondition', 'column'), array($db));
		
		$q->expects($this->at(0))->method('createCondition')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(array()))->will($this->returnCallback(array(__CLASS__, 'dummyCreateCondition')));
		$q->expects($this->at(1))->method('createCondition')->with($this->equalTo('baz'), $this->equalTo('lol'), $this->equalTo(array('foo$bar')))->will($this->returnCallback(array(__CLASS__, 'dummyCreateCondition')));
		
		$this->assertSame($q, $q->join('Table', array('foo' => 'bar', 'baz' => 'lol')));
		
		$this->assertEquals(array('LEFT JOIN esct AS et1 ON foo$bar baz$lol'), $q->join);
		$this->assertEquals(array('ecols'), $q->columns);
	}
	public function testJoin5()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'Prefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('PrefixTable')->will($this->returnValue('esct'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('t1')->will($this->returnValue('et1'));
		
		$q = $this->getMock('Db_Query_Select', array('createCondition', 'column'), array($db));
		
		$q->expects($this->once())->method('createCondition')->with($this->equalTo('foobar'), $this->isNull(), $this->equalTo(array()))->will($this->returnCallback(array(__CLASS__, 'dummyCreateCondition')));
		$q->expects($this->once())->method('column')->with('testcol', 't1', true);
		
		$this->assertSame($q, $q->join('Table', 'foobar', 'testcol'));
		
		$this->assertEquals(array('LEFT JOIN esct AS et1 ON foobar$'), $q->join);
		$this->assertEquals(array(), $q->columns);
	}
	public function testJoin6()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'Prefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('t1')->will($this->returnValue('et1'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('t1.*')->will($this->returnValue('ecols'));
		
		$sq = $this->getMock('Db_Query_Select', array('__toString'), array(new stdClass()));
		
		$sq->expects($this->once())->method('__toString')->will($this->returnValue('lolcat'));
		
		$q = $this->getMock('Db_Query_Select', array('createCondition', 'column'), array($db));
		
		$q->expects($this->once())->method('createCondition')->with($this->equalTo('foobar'), $this->isNull(), $this->equalTo(array()))->will($this->returnCallback(array(__CLASS__, 'dummyCreateCondition')));
		$q->expects($this->never())->method('column');
		
		$this->assertSame($q, $q->join($sq, 'foobar'));
		
		$this->assertEquals(array('LEFT JOIN (lolcat) AS et1 ON foobar$'), $q->join);
		$this->assertEquals(array('ecols'), $q->columns);
	}
	public function testJoin7()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'Prefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('PrefixTable')->will($this->returnValue('esct'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('t1')->will($this->returnValue('et1'));
		$db->expects($this->at(2))->method('protectIdentifiers')->with('t1.*')->will($this->returnValue('ecols'));
		
		$q = $this->getMock('Db_Query_Select', array('createCondition', 'column'), array($db));
		
		$q->expects($this->once())->method('createCondition')->with($this->equalTo('foobar'), $this->isNull(), $this->equalTo(array()))->will($this->returnCallback(array(__CLASS__, 'dummyCreateCondition')));
		
		$this->assertSame($q, $q->join('Table', 'foobar', false, 'right'));
		
		$this->assertEquals(array('RIGHT JOIN esct AS et1 ON foobar$'), $q->join);
		$this->assertEquals(array('ecols'), $q->columns);
	}
	public function testJoin8()
	{
		$db = $this->getMock('Db_Connection', array('protectIdentifiers'));
		
		$db->dbprefix = 'Prefix';
		
		$db->expects($this->at(0))->method('protectIdentifiers')->with('a')->will($this->returnValue('et1'));
		$db->expects($this->at(1))->method('protectIdentifiers')->with('a.*')->will($this->returnValue('ecols'));
		
		$sq = $this->getMock('Db_Query_Select', array('__toString'), array(new stdClass()));
		
		$sq->expects($this->once())->method('__toString')->will($this->returnValue('lolcat'));
		
		$q = $this->getMock('Db_Query_Select', array('createCondition', 'column'), array($db));
		
		$q->expects($this->once())->method('createCondition')->with($this->equalTo('foobar'), $this->isNull(), $this->equalTo(array()))->will($this->returnCallback(array(__CLASS__, 'dummyCreateCondition')));
		$q->expects($this->never())->method('column');
		
		$this->assertSame($q, $q->join(array('a' => $sq), 'foobar'));
		
		$this->assertEquals(array('LEFT JOIN (lolcat) AS et1 ON foobar$'), $q->join);
		$this->assertEquals(array('ecols'), $q->columns);
	}
	
	// ------------------------------------------------------------------------
	
	public function testIncomplete()
	{
		$this->markTestIncomplete('Select Query tests');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Dummy method for the Db_Query::createCondition() method.
	 * 
	 * @param  mixed
	 * @param  mixed
	 * @param  array
	 * @return void
	 */
	public static function dummyCreateCondition($col, $v, &$arr)
	{
		$arr[] = $col.'$'.$v;
	}
	
}

/* End of file SelectTest.php */
/* Location: ./tests/Db/Query */