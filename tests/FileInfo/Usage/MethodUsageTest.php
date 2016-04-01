<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Usage;

use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;

class MethodUsageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $methodUsage = new MethodUsage('method', 'class', 0, false);

        $this->assertInstanceOf(MethodUsage::class, $methodUsage);
    }

    public function testGetMethodName()
    {
        $methodUsage = new MethodUsage('method', 'class', 0, false);

        $this->assertSame('method', $methodUsage->name());
    }

    public function testGetClassName()
    {
        $methodUsage = new MethodUsage('method', 'class', 0, false);

        $this->assertSame('class', $methodUsage->className());
    }

    public function testGetLineNumber()
    {
        $methodUsage = new MethodUsage('method', 'class', 0, false);

        $this->assertSame(0, $methodUsage->getLineNumber());
    }

    public function testIsStatic()
    {
        $methodUsage = new MethodUsage('method', 'class', 0, false);
        $this->assertSame(false, $methodUsage->isStatic());

        $methodUsage = new MethodUsage('method', 'class', 0, true);
        $this->assertSame(true, $methodUsage->isStatic());
    }
}
