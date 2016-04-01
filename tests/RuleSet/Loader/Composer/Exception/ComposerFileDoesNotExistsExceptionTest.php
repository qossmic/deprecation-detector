<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer\Exception;

use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerException;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerFileDoesNotExistsException;

class ComposerFileDoesNotExistsExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $exception = new ComposerFileDoesNotExistsException('path/to/lock');
        $this->assertInstanceOf(ComposerException::class, $exception);
        $this->assertEquals('composer.lock file "path/to/lock" does not exist.', $exception->getMessage());
    }
}
