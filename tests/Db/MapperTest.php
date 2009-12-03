<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../lib/Db/Mapper.php';

/**
 * @covers Db_Mapper
 * @runTestsInSeparateProcesses enabled	
 * @preserveGlobalState disabled
 */
class Db_MapperTest extends PHPUnit_Framework_TestCase
{
	
	// ------------------------------------------------------------------------
	
	public function testIncomplete()
	{
		$this->markTestIncomplete('Db_Mapper test');
	}
}


/* End of file MapperTest.php */
/* Location: ./tests/Db */