====================================
RapidDataMapper           Comparison
====================================


This will be a simple comparison to the major opponents among the PHP ORMs.

Performance
===========

This is taken from the benchmark: http://code.google.com/p/php-orm-benchmark/

I've added RDM and modified the benchmark code to execute the commit code
within the timing method, to capture the Unit of Work processing too.

Result
------

Lower value is better, PDOTestSuite is a reference implementation which uses
PDO directly with prewritten SQL.

+-------------------------------+--------+--------+--------+--------+--------+
|Test Name                      | Insert | findPk | complex| hydrate|  with  |
+===============================+========+========+========+========+========+
|PDOTestSuite                   |    136 |    129 |     87 |    125 |    109 |
+-------------------------------+--------+--------+--------+--------+--------+
|Propel14TestSuite              |   3918 |   1577 |    290 |    883 |    897 |
+-------------------------------+--------+--------+--------+--------+--------+
|Propel15aLa14TestSuite         |   3598 |   1558 |    259 |    882 |    964 |
+-------------------------------+--------+--------+--------+--------+--------+
|Propel15TestSuite              |   3571 |   1967 |    335 |   1214 |   1266 |
+-------------------------------+--------+--------+--------+--------+--------+
|Propel15WithCacheTestSuite     |   3491 |   1368 |    346 |   1010 |    964 |
+-------------------------------+--------+--------+--------+--------+--------+
|Doctrine12TestSuite            |   8208 |  11893 |   1624 |   6483 |   7285 |
+-------------------------------+--------+--------+--------+--------+--------+
|Doctrine12WithCacheTestSuite   |   8439 |   4329 |   1677 |   3354 |   2487 |
+-------------------------------+--------+--------+--------+--------+--------+
|Doctrine2TestSuite             |   2870 |   1090 |   1048 |   3854 |   3706 |
+-------------------------------+--------+--------+--------+--------+--------+
|Doctrine2WithCacheTestSuite    |   2972 |   1129 |    161 |   2170 |   1274 |
+-------------------------------+--------+--------+--------+--------+--------+
|RapidDataMapper07TestSuite     |    657 |    536 |    150 |    446 |    490 |
+-------------------------------+--------+--------+--------+--------+--------+

As you can see, RapidDataMapper 0.7.0-dev is performing really well!

Hardware
--------

- MacBook Pro 17" from early 2009
- Intel Core 2 Duo 2.93Ghz
- 4 Gb RAM DDR3 1067Mhz
- Seagate Momentus 7200rpm 500 Gb 16 Mb

Software
--------

- Mac OS X Snow Leopard 10.6.3
- 64bit PHP 5.3.2
- APC 3.1.3p1 enabled
- SQLite library version 3.6.22


Declaring Entities and mapping them to the database
===================================================

TODO


Issuing queries
===============

TODO


Handling Relations
==================

TODO