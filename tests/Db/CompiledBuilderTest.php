<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../lib/Db/CodeBuilder/Container.php';
require_once dirname(__FILE__).'/../../lib/Db/CompiledBuilder.php';

/**
 * @covers Db_CompiledBuilder
 * @runTestsInSeparateProcesses enabled	
 * @preserveGlobalState disabled
 */
class Db_CompiledBuilderTest extends PHPUnit_Framework_TestCase
{
	
	// ------------------------------------------------------------------------
	
	public function testIncomplete()
	{
		$this->markTestIncomplete('Db_CompiledBuilder test');
	}
}


/* End of file CompiledBuilderTest.php */
/* Location: ./tests/Db */