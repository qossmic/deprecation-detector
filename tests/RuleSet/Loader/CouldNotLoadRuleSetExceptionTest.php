<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader;

use SensioLabs\DeprecationDetector\RuleSet\Loader\CouldNotLoadRuleSetException;

class CouldNotLoadRuleSetExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $exception = new CouldNotLoadRuleSetException('an exception message');

        $this->assertInstanceOf(
            CouldNotLoadRuleSetException::class,
            $exception
        );
    }

    public function testMessage()
    {
        $exception = new CouldNotLoadRuleSetException('an exception message');

        $this->assertSame('an exception message', $exception->getMessage());
    }
}
