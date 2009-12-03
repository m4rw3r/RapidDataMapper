<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../lib/Db/Connection.php';

/**
 * @covers Db_Connection
 * @runTestsInSeparateProcesses enabled	
 * @preserveGlobalState disabled
 */
class Db_ConnectionTest extends PHPUnit_Framework_TestCase
{
	
	// ------------------------------------------------------------------------
	
	public function testIncomplete()
	{
		$this->markTestIncomplete('Db_Connection test');
	}
}


/* End of file ConnectionTest.php */
/* Location: ./tests/Db */