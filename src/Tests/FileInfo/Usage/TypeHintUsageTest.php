<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Usage;

use SensioLabs\DeprecationDetector\FileInfo\Usage\TypeHintUsage;

class TypeHintUsageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $typeHintUsage = new TypeHintUsage('TypeHint', 1);

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\FileInfo\Usage\TypeHintUsage', $typeHintUsage);
    }

    public function testGetTypeHintName()
    {
        $typeHintUsage = new TypeHintUsage('TypeHint', 1);

        $this->assertSame('TypeHint', $typeHintUsage->name());
    }

    public function testGetLineNumber()
    {
        $typeHintUsage = new TypeHintUsage('TypeHint', 1);

        $this->assertSame(1, $typeHintUsage->getLineNumber());
    }
}
