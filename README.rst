===============
RapidDataMapper
===============

A DataMapper and database abstraction for PHP.

Requirements
============

* PHP > 5.2
* PHPUnit 4.0 dev (for running the test suite), can be found here_

.. _here: http://www.phpunit.de/wiki/SubversionRepository

Loading of files
================

RapidDataMapper is trying to use the file organization which is also used by many other
PHP libraries; That is, convert all underscored (and namespace separators) in the
class name to DIRECTORY_SEPARATOR.
That means the class Db_Driver_Mysql_Connection is located in the file
Db/Driver/Mysql/Connection.php.

RapidDataMapper includes an autoloder which will autoload the library files, relative
to the library root. This autoloader is not automatically initialized, as some frameworks
or users already utilizes a compatible autoloader, so you have to call its initializing method::

    // load the library file
    require 'lib/Db.php';
    
    // init the autoloader packaged with the library
    Db::initAutoload();

The mapping from class name to file name is made like this::

    $file = self::$lib_base . str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';

Running the tests
=================

1. Make sure that the tests directory is placed in the same directory as
   the lib folder.
2. Run the tests by executing "PHPUnit tests" in that directory