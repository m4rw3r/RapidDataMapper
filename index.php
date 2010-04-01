<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

echo "<pre>";

require 'config.php';

$c = new \ExampleCollection;

$c->has()
		->a('b')
		->c('d')
	->end()
	->orHas()
		->a('d')
		->c('b')
	->end();

print_r($c);

var_dump((String) $c);

foreach($c as $v)
{
	var_dump($v);
}

foreach($c as $v)
{
	var_dump($v);
}


/* End of file index.php */
/* Location: . */