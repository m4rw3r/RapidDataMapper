<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../lib/Db/IdentityMap.php';

/**
 * @covers Db_IdentityMap
 * @runTestsInSeparateProcesses enabled	
 * @preserveGlobalState disabled
 */
class Db_IdentityMapTest extends PHPUnit_Framework_TestCase
{
	
	// ------------------------------------------------------------------------
	
	public function testIncomplete()
	{
		$this->markTestIncomplete('Db_IdentityMap test');
	}
}


/* End of file IdentityMapTest.php */
/* Location: ./tests/Db */