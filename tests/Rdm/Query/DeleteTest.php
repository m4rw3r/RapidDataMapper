<?php
/*
 * Created by Martin Wernståhl on 2009-09-04.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once dirname(__FILE__).'/../../../lib/Rdm/Query/Abstract.php';
require_once dirname(__FILE__).'/../../../lib/Rdm/Query/Delete.php';

/**
 * @covers Rdm_Query_Delete
 * @runTestsInSeparateProcesses enabled
 * @preserveGlobalState disabled
 */
class Rdm_Query_DeleteTest extends PHPUnit_Framework_TestCase
{
	// ------------------------------------------------------------------------
	
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../../lib/Rdm/Exception.php';
		require_once dirname(__FILE__).'/../../../lib/Rdm/Query/BuilderException.php';
	}
	
	// ------------------------------------------------------------------------
	
	public function testEmpty()
	{
		$this->markTestIncomplete('Delete query tests.');
	}
}

/* End of file DeleteTest.php */
/* Location: ./tests/Rdm/Query */