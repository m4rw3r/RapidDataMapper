<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../lib/Db.php';

/**
 * Tests the main Db object.
 */
class DbTest extends PHPUnit_Framework_TestCase
{
	/**
	 * The Db class is a singleton, hence we need to restart PHP
	 * every time to make sure that it is reset
	 */
	public $runTestInSeparateProcess = true;
	
	
}


/* End of file Db.php */
/* Location: ./tests/DbTest.php */