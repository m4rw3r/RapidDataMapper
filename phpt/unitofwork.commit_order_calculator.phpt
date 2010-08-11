--TEST--
Test the CommitOrderCalculator
--FILE--
<?php

// Copy from config/config.php
error_reporting(E_ALL | E_STRICT | E_DEPRECATED);
require dirname(__FILE__).'/../lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();

$c = new Rdm_UnitOfWork_CommitOrderCalculator();

$c->dependencies = array('a' => array(), 'b' => array('a'));

var_dump($c->calculate());

$c->dependencies = array('b' => array('a'), 'a' => array());

var_dump($c->calculate());

--EXPECT--
array(2) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) "b"
}
array(2) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) "b"
}