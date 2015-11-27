<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Usage;

use SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage;

class FunctionUsageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $functionUsage = new FunctionUsage('name', 0);

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage', $functionUsage);
    }

    public function testGetClassName()
    {
        $functionUsage = new FunctionUsage('name', 0);

        $this->assertSame('name', $functionUsage->name());
    }

    public function testGetLineNumber()
    {
        $functionUsage = new FunctionUsage('name', 0);

        $this->assertSame(0, $functionUsage->getLineNumber());
    }
}
