<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\InterfaceViolationChecker;

class InterfaceViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $checker = new InterfaceViolationChecker($ruleSet->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\ViolationChecker\InterfaceViolationChecker', $checker);
    }

    public function testCheck()
    {
        $interfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $interfaceUsage->name()->willReturn('interface');

        $deprecatedInterfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $deprecatedInterfaceUsage->className()->willReturn('class');
        $deprecatedInterfaceUsage->name()->willReturn('deprecatedInterface');
        $deprecatedInterfaceUsage->getLineNumber()->willReturn(10);

        $interfaceUsage = $interfaceUsage->reveal();
        $deprecatedInterfaceUsage = $deprecatedInterfaceUsage->reveal();

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $file = $this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal();
        $collection = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $collection->interfaceUsages()->willReturn(
            array('class' => array(
            $interfaceUsage,
            $deprecatedInterfaceUsage,
            ))
        );
            $collection = $collection->reveal();

            $ruleSet->hasInterface('interface')->willReturn(false);
            $ruleSet->hasInterface('deprecatedInterface')->willReturn(true);

            $deprecationComment = 'comment';
            $interfaceDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation');
            $interfaceDeprecation->comment()->willReturn($deprecationComment);

            $ruleSet->getInterface('deprecatedInterface')->willReturn($interfaceDeprecation->reveal());

            $checker = new InterfaceViolationChecker($ruleSet->reveal());

            $this->assertEquals(array(
            new Violation($deprecatedInterfaceUsage, $collection, $deprecationComment),
            ), $checker->check($collection));
    }
}
