--TEST--
Annotation: Consecutive calls to readAnnotations with the same class/method/property/function should result in the exact same objects returned
--FILE--
<?php

require __DIR__.'/../lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();

class TestAnnotation extends Rdm_Util_Annotation_AbstractAnnotation
{
	// Empty
}

#[TestAnnotation]
class Foo
{
	
}

$factory = new Rdm_Util_Annotation_Factory();
$reader  = new Rdm_Util_Annotation_Reader($factory);

$data = $reader->readAnnotations(new ReflectionClass('Foo'));

$data2 = $reader->readAnnotations(new ReflectionClass('Foo'));

var_dump($data);

var_dump($data2);

var_dump($data[0] === $data2[0]);


--EXPECTF--
array(1) {
  [0]=>
  object(TestAnnotation)#%d (0) {
  }
}
array(1) {
  [0]=>
  object(TestAnnotation)#%d (0) {
  }
}
bool(true)