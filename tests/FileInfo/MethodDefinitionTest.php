<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo;

use SensioLabs\DeprecationDetector\FileInfo\MethodDefinition;

class MethodDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $methodDefinition = new MethodDefinition('class', 'someParentClass', 0);

        $this->assertInstanceOf(MethodDefinition::class, $methodDefinition);
    }

    public function testName()
    {
        $methodDefinition = new MethodDefinition('class', 'someParentClass', 0);

        $this->assertSame('class', $methodDefinition->name());
    }

    public function testParentName()
    {
        $methodDefinition = new MethodDefinition('class', 'someParentClass', 0);

        $this->assertSame('someParentClass', $methodDefinition->parentName());
    }

    public function testGetLineNumber()
    {
        $methodDefinition = new MethodDefinition('class', 'someParentClass', 0);

        $this->assertSame(0, $methodDefinition->getLineNumber());
    }
}
