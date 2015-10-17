<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet;

use SensioLabs\DeprecationDetector\RuleSet\RuleSet;

class RuleSetTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = new RuleSet();

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\RuleSet', $ruleSet);
    }

    public function testMergeFileDeprecationCollection()
    {
        $ruleSet = new RuleSet();

        $deprecationCollection = $this->prophesize('SensioLabs\DeprecationDetector\DeprecationCollectionInterface');
        $deprecationCollection->classDeprecations()->willReturn(array())->shouldBeCalled();
        $deprecationCollection->interfaceDeprecations()->willReturn(array())->shouldBeCalled();
        $deprecationCollection->methodDeprecations()->willReturn(array())->shouldBeCalled();

        $ruleSet->merge($deprecationCollection->reveal());
    }

    public function testClassDeprecations()
    {
        $classDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation');
        $classDeprecations = ['class' => $classDeprecation->reveal()];
        $ruleSet = new RuleSet($classDeprecations);

        $this->assertSame($classDeprecations, $ruleSet->classDeprecations());
    }

    public function testHasClass()
    {
        $classDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation');
        $classDeprecations = ['class' => $classDeprecation->reveal()];

        $ruleSet = new RuleSet($classDeprecations);
        $this->assertTrue($ruleSet->hasClass('class'));
        $this->assertFalse($ruleSet->hasClass('someOtherClass'));
    }

    public function testGetClass()
    {
        $classDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation');
        $classDeprecations = ['class' => $classDeprecation->reveal()];

        $ruleSet = new RuleSet($classDeprecations);
        $this->assertSame($classDeprecation->reveal(), $ruleSet->getClass('class'));
        $this->assertNull($ruleSet->getClass('not existing'));
    }

    public function testInterfaceDeprecations()
    {
        $interfaceDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation');
        $interfaceDeprecations = ['interface' => $interfaceDeprecation->reveal()];

        $ruleSet = new RuleSet([], $interfaceDeprecations);
        $this->assertSame($interfaceDeprecations, $ruleSet->interfaceDeprecations());
    }

    public function testHasInterface()
    {
        $interfaceDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation');
        $interfaceDeprecations = ['interface' => $interfaceDeprecation->reveal()];

        $ruleSet = new RuleSet([], $interfaceDeprecations);
        $this->assertTrue($ruleSet->hasInterface('interface'));
        $this->assertFalse($ruleSet->hasInterface('someOtherInterface'));
    }

    public function testGetInterface()
    {
        $interfaceDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation');
        $interfaceDeprecations = ['interface' => $interfaceDeprecation->reveal()];

        $ruleSet = new RuleSet([], $interfaceDeprecations);
        $this->assertSame($interfaceDeprecation->reveal(), $ruleSet->getInterface('interface'));
        $this->assertNull($ruleSet->getInterface('someOtherInterface'));
    }

    public function testMethodDeprecations()
    {
        $methodDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation');
        $methodDeprecations = ['class' => [$methodDeprecation->reveal()]];
        $ruleSet = new RuleSet([], [], $methodDeprecations);

        $this->assertSame($methodDeprecations, $ruleSet->methodDeprecations());
    }

    public function testHasMethod()
    {
        $methodDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation');
        $methodDeprecations = ['class' => ['method' => $methodDeprecation->reveal()]];
        $ruleSet = new RuleSet([], [], $methodDeprecations);

        $this->assertTrue($ruleSet->hasMethod('method', 'class'));
        $this->assertFalse($ruleSet->hasMethod('someOtherMethod', 'class'));
    }

    public function testGetMethod()
    {
        $methodDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation');
        $methodDeprecations = ['class' => ['method' => $methodDeprecation->reveal()]];
        $ruleSet = new RuleSet([], [], $methodDeprecations);

        $this->assertSame($methodDeprecation->reveal(), $ruleSet->getMethod('method', 'class'));
        $this->assertNull($ruleSet->getMethod('someOtherMethod', 'class'));
    }
}
