<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer\Exception;

use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerException;

class ComposerExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $exception = new ComposerException();
        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerException', $exception);
    }
}
