<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Usage;

use SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage;

class SuperTypeUsageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $superTypeUsage = new SuperTypeUsage('superType', 'class', 0);

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage', $superTypeUsage);
    }

    public function testGetSuperTypeName()
    {
        $superTypeUsage = new SuperTypeUsage('superType', 'class', 0);

        $this->assertSame('superType', $superTypeUsage->name());
    }

    public function testGetClassName()
    {
        $superTypeUsage = new SuperTypeUsage('superType', 'class', 0);

        $this->assertSame('class', $superTypeUsage->className());
    }

    public function testGetLineNumber()
    {
        $superTypeUsage = new SuperTypeUsage('superType', 'class', 0);

        $this->assertSame(0, $superTypeUsage->getLineNumber());
    }
}
