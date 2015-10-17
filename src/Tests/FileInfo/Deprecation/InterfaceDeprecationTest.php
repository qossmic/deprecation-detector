<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Deprecation;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation;

class InterfaceDeprecationTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $interfaceDeprecation = new InterfaceDeprecation('interfaceName', 'comment');

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation', $interfaceDeprecation);
    }

    public function testGetInterfaceName()
    {
        $interfaceDeprecation = new InterfaceDeprecation('interfaceName', 'comment');

        $this->assertSame('interfaceName', $interfaceDeprecation->name());
    }

    public function testGetComment()
    {
        $interfaceDeprecation = new InterfaceDeprecation('interfaceName', 'comment');

        $this->assertSame('comment', $interfaceDeprecation->comment());
    }
}
