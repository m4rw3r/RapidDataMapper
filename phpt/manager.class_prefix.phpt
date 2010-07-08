--TEST--
Try to get a Collection from a manager which filters by a namespace
--FILE--
<?php

// Copy from config/config.php
error_reporting(E_ALL | E_STRICT | E_DEPRECATED);
require dirname(__FILE__).'/../lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();

$adapter = new Rdm_Adapter_SqLite(array(
	'file'     => ':memory:',
	'dbprefix' => 'tbl_'
	));

$config = new Rdm_Config();
$config->setAdapter($adapter);

// Initialize <Class>Collection autoloaders with prefix filter
$manager = new Rdm_CollectionManager($config, 'Model\\');
$manager->registerCollectionAutoloader(false);

// Get two different groups of entities
include 'entities/NamespacedArtistAlbumTrack.php';
include 'entities/ArtistAlbumTrack.php';

// Try to create the classes, the non-namespaced variant should fail
var_dump(class_exists('ArtistCollection'));
var_dump(class_exists('Model\\ArtistCollection'));

--EXPECT--
bool(false)
bool(true)