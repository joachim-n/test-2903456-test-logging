This is a proof-of-concept of how PHPUnit tests can be made to fail when the SUT
logs an error.

This is desirable functionality because there are often places in a system where
the system will recover from a failure, log an error and continue. In some
cases, we wish to specifically test for this pathway, but in others we want to
know that the SUT is performing without errors at all.

It is therefore useful for a PHPUnit test to be able to:
  - assert that a certain error should be logged
  - assert that no errors should be logged.

Asserting no log errors is complicated by the fact that often, an error is
logged within a try/catch block, and PHPUnit's Assert::fail() works by throwing
an exception. This gets caught by the SUT's catch, and therefore does not make
it back to PHPUnit.

This proof-of-concept shows a way to handle this particular case, but it's not
ideal. It would be easier to handle this if this functionality was part of
PHPUnit.
