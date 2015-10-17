<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Deprecation;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation;

class MethodDeprecationTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $methodDeprecation = new MethodDeprecation('className', 'methodName', 'comment');

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation',
            $methodDeprecation
        );
    }

    public function testGetClassName()
    {
        $methodDeprecation = new MethodDeprecation('className', 'methodName', 'comment');

        $this->assertSame('className', $methodDeprecation->parentName());
    }

    public function testGetMethodName()
    {
        $methodDeprecation = new MethodDeprecation('className', 'methodName', 'comment');

        $this->assertSame('methodName', $methodDeprecation->name());
    }

    public function testGetComment()
    {
        $methodDeprecation = new MethodDeprecation('className', 'methodName', 'comment');

        $this->assertSame('comment', $methodDeprecation->comment());
    }
}
