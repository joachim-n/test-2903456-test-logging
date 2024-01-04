<?php

namespace Joachim\Test2903456TestLogging\Test;

use Joachim\Test2903456TestLogging\Sut;
use PhpExtended\Logger\RethrowMultipleLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Demonstration of cases where we want a logged error to fail the test.
 *
 * In all cases, we want the test to fail due to the logged error. However, this
 * doesn't work properly in cases where the SUT emits the log inside a try/catch
 * block.
 */
class LogImmediateFailureTest extends TestCase implements LoggerInterface {
  use LoggerTrait;

  protected $logger;

  public function log($level, $message, array $context = array()) {
    $this->fail('Log error!');
  }

  public function setUp(): void {
    $this->logger = new RethrowMultipleLogger(array($this));
  }

  public function testPlainLogErrorWithSuccess() {
    $sut = new Sut($this->logger);

    // This will fail the test as expected.
    $result = $sut->plainLogErrorWithSuccess();

    // We don't get here. Developer must fix the log error first!
    $this->assertEquals('good', $result, 'Bad result from SUT');
  }

  public function testPlainLogErrorWithFailure() {
    $sut = new Sut($this->logger);

    // This will fail the test as expected.
    $result = $sut->plainLogErrorWithFailure();

    // We don't get here. Developer must fix the log error first, and then they
    // will have to fix this too.
    $this->assertEquals('good', $result, 'Bad result from SUT');
  }

  public function testCaughtLogErrorWithSuccess() {
    $sut = new Sut($this->logger);

    // This will NOT fail the test, which is not the result we want!
    $result = $sut->caughtErrorWithSuccess();

    // This will pass the test. But the log error is a problem which is being
    // obscured from the developer, which is bad!
    $this->assertEquals('good', $result, 'Bad result from SUT');
  }

  public function testCaughtErrorWithFailure() {
    $sut = new Sut($this->logger);

    // This will NOT fail the test, which is not the result we want!
    $result = $sut->caughtErrorWithFailure();

    // This will fail the test. However, the log error which might be
    // responsible for this return value is being obscured from the developer,
    // which is bad!
    $this->assertEquals('good', $result, 'Bad result from SUT');
  }

}