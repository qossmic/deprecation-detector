<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\InterfaceViolationChecker;

class InterfaceViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = $this->prophesize(RuleSet::class);
        $checker = new InterfaceViolationChecker($ruleSet->reveal());

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\ViolationChecker\InterfaceViolationChecker',
            $checker
        );
    }

    public function testCheck()
    {
        $interfaceUsage = $this->prophesize(InterfaceUsage::class);
        $interfaceUsage->name()->willReturn('interface');

        $deprecatedInterfaceUsage = $this->prophesize(InterfaceUsage::class);
        $deprecatedInterfaceUsage->className()->willReturn('class');
        $deprecatedInterfaceUsage->name()->willReturn('deprecatedInterface');
        $deprecatedInterfaceUsage->getLineNumber()->willReturn(10);

        $interfaceUsage = $interfaceUsage->reveal();
        $deprecatedInterfaceUsage = $deprecatedInterfaceUsage->reveal();

        $ruleSet = $this->prophesize(RuleSet::class);
        $collection = $this->prophesize(PhpFileInfo::class);
        $collection->interfaceUsages()->willReturn([
            'class' => [
                $interfaceUsage,
                $deprecatedInterfaceUsage,
            ]
        ]);
        $collection = $collection->reveal();

        $ruleSet->hasInterface('interface')->willReturn(false);
        $ruleSet->hasInterface('deprecatedInterface')->willReturn(true);

        $deprecationComment = 'comment';
        $interfaceDeprecation = $this
            ->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation');
        $interfaceDeprecation->comment()->willReturn($deprecationComment);

        $ruleSet->getInterface('deprecatedInterface')->willReturn($interfaceDeprecation->reveal());

        $checker = new InterfaceViolationChecker();

        $this->assertEquals([
            new Violation($deprecatedInterfaceUsage, $collection, $deprecationComment),
        ], $checker->check($collection, $ruleSet->reveal()));
    }
}
