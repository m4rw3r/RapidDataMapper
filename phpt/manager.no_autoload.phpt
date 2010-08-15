--TEST--
Test to see that the autoloader is added at the appropriate time, ie. using the Rdm_CollectionManager->registerCollectionAutoloader()
--FILE--
<?php

include dirname(__FILE__).'/../lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();

$c = new Rdm_Config();

$m = new Rdm_CollectionManager($c);

$l = new FooCollection();

--EXPECTF--
Fatal error: Class 'FooCollection' not found in %s