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
 * block, so we need to check in tearDown().
 */
class LogImmediateFailureTest extends TestCase implements LoggerInterface {
  use LoggerTrait;

  protected $logger;

  /**
   * Stores all logged messages.
   *
   * Flat array. In a real world situation, we'd have these grouped by level.
   *
   * @var array
   */
  protected $logMessages = [];

  /**
   * Tracks whether a logged error has caused a failure.
   *
   * This is necessary for the case where the log error was emitted inside a
   * try/catch block, which catches the test failure exception.
   *
   * @var bool
   */
  protected bool $logErrorCausedFailure = FALSE;

  public function log($level, $message, array $context = array()) {
    $this->logMessages[] = $message;

    // Keep track of the failure, in case the Assert::fail() is swallowed by a
    // catch().
    $this->logErrorCausedFailure = TRUE;
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
    // tearDown() will redress this.
    $this->assertEquals('good', $result, 'Bad result from SUT');
  }

  public function testCaughtErrorWithFailure() {
    $sut = new Sut($this->logger);

    // This will NOT fail the test, which is not the result we want!
    $result = $sut->caughtErrorWithFailure();

    // This will fail the test. However, the log error which might be
    // responsible for this return value is being obscured from the developer,
    // which is bad!
    // tearDown() will redress this.
    $this->assertEquals('good', $result, 'Bad result from SUT');
  }

  protected function tearDown(): void {
    // Ensure that if the test produced logs, it actually fails.  This is to
    // cover the case where the error was logged in the SUT from inside a
    // try/catch block.
    // TODO: Give the backtrace of the logged error.
    if ($this->logErrorCausedFailure) {
      if ($this->status()->isFailure()) {
        if (!str_contains($this->status()->message(), 'Log error')) {
          $this->fail("The test failed but the failure obscured a log error.");
        }
      }
      else {
        $this->fail("The test passed, but there were errors logged.");
      }
    }
  }

}
