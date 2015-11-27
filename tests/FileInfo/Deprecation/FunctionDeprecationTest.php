<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Deprecation;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\FunctionDeprecation;

class FunctionDeprecationTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $functionDeprecation = new FunctionDeprecation('functionName', 'comment');

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\FileInfo\Deprecation\FunctionDeprecation',
            $functionDeprecation
        );
    }

    public function testGetInterfaceName()
    {
        $functionDeprecation = new FunctionDeprecation('functionName', 'comment');

        $this->assertSame('functionName', $functionDeprecation->name());
    }

    public function testGetComment()
    {
        $functionDeprecation = new FunctionDeprecation('functionName', 'comment');

        $this->assertSame('comment', $functionDeprecation->comment());
    }
}
