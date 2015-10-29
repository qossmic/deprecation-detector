<?php

namespace SensioLabs\DeprecationDetector\tests\RuleSet\Loader;

use SensioLabs\DeprecationDetector\RuleSet\Loader\CouldNotLoadRuleSetException;

class CouldNotLoadRuleSetExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $exception = new CouldNotLoadRuleSetException('an exception message');

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\RuleSet\Loader\CouldNotLoadRuleSetException',
            $exception
        );
    }

    public function testMessage()
    {
        $exception = new CouldNotLoadRuleSetException('an exception message');

        $this->assertSame('<error>an exception message</error>', $exception->getMessage());
    }
}
