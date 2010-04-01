<?php
/*
 * Created by Martin Wernståhl on 2010-04-01.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

// Register RapidDataMapper's default autoloader implementation
require 'lib/Rdm/Util/Autoloader.php';
\Rdm\Util\Autoloader::init();

// Register the example loader
spl_autoload_register('exampleloader');
function exampleloader($class)
{
	require ltrim(strtr($class, '\\_', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR).'.php', DIRECTORY_SEPARATOR);
}

/* End of file config.php */
/* Location: . */