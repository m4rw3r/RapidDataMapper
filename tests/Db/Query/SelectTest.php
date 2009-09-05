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
		require_once dirname(__FILE__).'/../../../lib/Db/Query.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Query/Select.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception.php';
		require_once dirname(__FILE__).'/../../../lib/Db/Exception/QueryIncomplete.php';
	}
	
	// ------------------------------------------------------------------------
	
	public function testEmpty()
	{
		$this->markTestIncomplete('Select query tests.');
	}
}

/* End of file SelectTest.php */
/* Location: ./tests/Db/Query */