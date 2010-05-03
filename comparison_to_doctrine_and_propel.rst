====================================
RapidDataMapper           Comparison
====================================


This will be a simple comparison to the major opponents among the PHP ORMs.

Performance
===========

This is taken from the benchmark: http://code.google.com/p/php-orm-benchmark/

I've added RDM and modified the benchmark code to execute the commit code
within the timing method, to capture the Unit of Work processing too.

                             | Insert | findPk | complex| hydrate|  with  |
                             |--------|--------|--------|--------|--------|
                PDOTestSuite |    137 |    128 |     89 |    120 |    100 |
           Propel14TestSuite |   3917 |   1574 |    259 |    889 |    875 |
      Propel15aLa14TestSuite |   3594 |   1559 |    255 |    881 |    954 |
           Propel15TestSuite |   3572 |   1972 |    472 |   1203 |   1263 |
  Propel15WithCacheTestSuite |   3489 |   1366 |    341 |   1008 |    964 |
         Doctrine12TestSuite |   8207 |  11941 |   1619 |   6467 |   7311 |
Doctrine12WithCacheTestSuite |   8478 |   4361 |   1676 |   3362 |   2504 |
          Doctrine2TestSuite |   2857 |   1088 |   1089 |   3780 |   3703 |
 Doctrine2WithCacheTestSuite |   2973 |   1122 |    162 |   2098 |   1281 |
  RapidDataMapper07TestSuite |    656 |    534 |    173 |    444 |    491 |

As you can see, RapidDataMapper 0.7-dev is performing really well!


Declaring Entities and mapping them to the database
===================================================

TODO


Issuing queries
===============

TODO


Handling Relations
==================

TODO