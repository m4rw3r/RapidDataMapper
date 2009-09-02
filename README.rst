===============
RapidDataMapper
===============

A DataMapper and database abstraction for PHP.

Requirements
============

RapidDataMapper:

* PHP > 5.2
* Appropriate database extension

Tests:

* PHPUnit 4.0 dev, can be found here_
* PHP > 5.3

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

Currently some of the tests (around 20) does not work because the PHPUnit option
@runInSeparateProcess includes all files that were included in the parent thread when
running the new thread, working on how to solve that.

Building the API documentation
==============================

Default template is the `EXT JS template`_ by Zym.

.. _`EXT JS template`: http://www.zymengine.com/dev/news/30-phpdoc-extjs-converter-template

the ./phpdoc file contains defualt settings