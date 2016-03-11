<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Usage;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;

class ClassUsageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $classUsage = new ClassUsage('name', 0);

        $this->assertInstanceOf(ClassUsage::class, $classUsage);
    }

    public function testGetClassName()
    {
        $classUsage = new ClassUsage('name', 0);

        $this->assertSame('name', $classUsage->name());
    }

    public function testGetLineNumber()
    {
        $classUsage = new ClassUsage('string', 0);

        $this->assertSame(0, $classUsage->getLineNumber());
    }
}
