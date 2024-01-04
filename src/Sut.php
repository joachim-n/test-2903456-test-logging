<?php

namespace Joachim\Test2903456TestLogging;

/**
 * The SUT.
 *
 * This has methods which either return a good or a bad result.
 *
 * Before returning its result, it logs an error. In some cases, the error
 * is logged inside a try/catch block, and in some cases it isn't.
 */
class Sut {

  public function __construct(
    protected $logger
  ) {

  }

  function plainLogErrorWithSuccess() {
    // SUT logs an error - we want this to fail the test.
    $this->logger->error('error');

    // The SUT returns a success.
    return 'good';
   }

  function plainLogErrorWithFailure() {
    // SUT logs an error - we want this to fail the test.
    $this->logger->error('error');

    // Once we've fixed that the log error, we then have to deal with the bad
    // return.
    return 'bad';
   }

   function caughtErrorWithSuccess() {
    try {
      // SUT logs an error - we want this to fail the test.
      $this->logger->error('error');
    }
    catch (\Exception $e) {
      // This will catch any attempt at failing the test from within the logger.
    }

    // This will pass the test -- but we want the test to fail!
    return 'good';
   }

   function caughtErrorWithFailure() {
    try {
      // SUT logs an error - we want this to fail the test.
      $this->logger->error('error');
    }
    catch (\Exception $e) {
      // This will catch any attempt at failing the test from within the logger.
    }

    // This will fail the test -- but we want the log to fail the test before
    // this does!
    return 'bad';
   }

}
