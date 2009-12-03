<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../lib/Db/Util.php';

/**
 * @covers Db_Util
 * @runTestsInSeparateProcesses enabled	
 * @preserveGlobalState disabled
 */
class Db_UtilTest extends PHPUnit_Framework_TestCase
{
	
	// ------------------------------------------------------------------------
	
	public function testIncomplete()
	{
		$this->markTestIncomplete('Db_Util test');
	}
}


/* End of file UtilTest.php */
/* Location: ./tests/Db */