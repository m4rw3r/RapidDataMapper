<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../lib/Db/Result.php';

/**
 * @covers Db_Result
 * @runTestsInSeparateProcesses enabled	
 * @preserveGlobalState disabled
 */
class Db_ResultTest extends PHPUnit_Framework_TestCase
{
	
	// ------------------------------------------------------------------------
	
	public function testIncomplete()
	{
		$this->markTestIncomplete('Db_Result test');
	}
}


/* End of file ResultTest.php */
/* Location: ./tests/Db */