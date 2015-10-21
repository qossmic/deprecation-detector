<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Usage;

use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;

class InterfaceUsageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $interfaceUsage = new InterfaceUsage('interface', 'class', 0);

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage', $interfaceUsage);
    }

    public function testGetInterfaceName()
    {
        $interfaceUsage = new InterfaceUsage('interface', 'class', 0);

        $this->assertSame('interface', $interfaceUsage->name());
    }

    public function testGetClassName()
    {
        $interfaceUsage = new InterfaceUsage('interface', 'class', 0);

        $this->assertSame('class', $interfaceUsage->className());
    }

    public function testGetLineNumber()
    {
        $interfaceUsage = new InterfaceUsage('interface', 'class', 0);

        $this->assertSame(0, $interfaceUsage->getLineNumber());
    }
}
