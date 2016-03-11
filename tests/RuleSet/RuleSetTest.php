<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\DeprecationCollectionInterface;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;

class RuleSetTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = new RuleSet();

        $this->assertInstanceOf(RuleSet::class, $ruleSet);
    }

    public function testMergeFileDeprecationCollection()
    {
        $ruleSet = new RuleSet();

        $deprecationCollection = $this->prophesize(DeprecationCollectionInterface::class);
        $deprecationCollection->classDeprecations()->willReturn(array())->shouldBeCalled();
        $deprecationCollection->interfaceDeprecations()->willReturn(array())->shouldBeCalled();
        $deprecationCollection->methodDeprecations()->willReturn(array())->shouldBeCalled();
        $deprecationCollection->functionDeprecations()->willReturn(array())->shouldBeCalled();

        $ruleSet->merge($deprecationCollection->reveal());
    }

    public function testClassDeprecations()
    {
        $classDeprecation = $this->prophesize(ClassDeprecation::class);
        $classDeprecations = array('class' => $classDeprecation->reveal());
        $ruleSet = new RuleSet($classDeprecations);

        $this->assertSame($classDeprecations, $ruleSet->classDeprecations());
    }

    public function testHasClass()
    {
        $classDeprecation = $this->prophesize(ClassDeprecation::class);
        $classDeprecations = array('class' => $classDeprecation->reveal());

        $ruleSet = new RuleSet($classDeprecations);
        $this->assertTrue($ruleSet->hasClass('class'));
        $this->assertFalse($ruleSet->hasClass('someOtherClass'));
    }

    public function testGetClass()
    {
        $classDeprecation = $this->prophesize(ClassDeprecation::class);
        $classDeprecations = array('class' => $classDeprecation->reveal());

        $ruleSet = new RuleSet($classDeprecations);
        $this->assertSame($classDeprecation->reveal(), $ruleSet->getClass('class'));
        $this->assertNull($ruleSet->getClass('not existing'));
    }

    public function testInterfaceDeprecations()
    {
        $interfaceDeprecation = $this
            ->prophesize(InterfaceDeprecation::class);
        $interfaceDeprecations = array('interface' => $interfaceDeprecation->reveal());

        $ruleSet = new RuleSet(array(), $interfaceDeprecations);
        $this->assertSame($interfaceDeprecations, $ruleSet->interfaceDeprecations());
    }

    public function testHasInterface()
    {
        $interfaceDeprecation = $this
            ->prophesize(InterfaceDeprecation::class);
        $interfaceDeprecations = array('interface' => $interfaceDeprecation->reveal());

        $ruleSet = new RuleSet(array(), $interfaceDeprecations);
        $this->assertTrue($ruleSet->hasInterface('interface'));
        $this->assertFalse($ruleSet->hasInterface('someOtherInterface'));
    }

    public function testGetInterface()
    {
        $interfaceDeprecation = $this
            ->prophesize(InterfaceDeprecation::class);
        $interfaceDeprecations = array('interface' => $interfaceDeprecation->reveal());

        $ruleSet = new RuleSet(array(), $interfaceDeprecations);
        $this->assertSame($interfaceDeprecation->reveal(), $ruleSet->getInterface('interface'));
        $this->assertNull($ruleSet->getInterface('someOtherInterface'));
    }

    public function testMethodDeprecations()
    {
        $methodDeprecation = $this->prophesize(MethodDeprecation::class);
        $methodDeprecations = array('class' => array($methodDeprecation->reveal()));
        $ruleSet = new RuleSet(array(), array(), $methodDeprecations);

        $this->assertSame($methodDeprecations, $ruleSet->methodDeprecations());
    }

    public function testHasMethod()
    {
        $methodDeprecation = $this->prophesize(MethodDeprecation::class);
        $methodDeprecations = array('class' => array('method' => $methodDeprecation->reveal()));
        $ruleSet = new RuleSet(array(), array(), $methodDeprecations);

        $this->assertTrue($ruleSet->hasMethod('method', 'class'));
        $this->assertFalse($ruleSet->hasMethod('someOtherMethod', 'class'));
    }

    public function testGetMethod()
    {
        $methodDeprecation = $this->prophesize(MethodDeprecation::class);
        $methodDeprecations = array('class' => array('method' => $methodDeprecation->reveal()));
        $ruleSet = new RuleSet(array(), array(), $methodDeprecations);

        $this->assertSame($methodDeprecation->reveal(), $ruleSet->getMethod('method', 'class'));
        $this->assertNull($ruleSet->getMethod('someOtherMethod', 'class'));
    }
}
