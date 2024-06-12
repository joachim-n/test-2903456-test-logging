<?php

namespace Joachim\Test2903456TestLogging;

/**
 * The SUT.
 *
 * This has two orthogonal cases:
 *  - The method either returns a good or a bad result.
 *  - The method logs an error, either inside a try/catch block, or without
 *    catching.
 */
class Sut {

  public function __construct(
    protected $logger
  ) {

  }

  /**
   * Logs an error without catching, and results in success.
   */
  function plainLogErrorWithSuccess() {
    // SUT logs an error - we want this to fail the test.
    $this->logger->error('error');

    // The SUT returns a success.
    return 'good';
   }

  /**
   * Logs an error without catching, and results in failure.
   */
  function plainLogErrorWithFailure() {
    // SUT logs an error - we want this to fail the test.
    $this->logger->error('error');

    // Once we've fixed that the log error, we then have to deal with the bad
    // return.
    return 'bad';
   }

  /**
   * Logs an error inside a catch, and results in success.
   */
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

  /**
   * Logs an error inside a catch, and results in failure.
   */
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
