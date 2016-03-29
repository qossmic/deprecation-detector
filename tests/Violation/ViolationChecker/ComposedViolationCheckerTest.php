<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ComposedViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface;

class ComposedViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $checker = new ComposedViolationChecker([]);

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\ViolationChecker\ComposedViolationChecker',
            $checker
        );
    }

    public function testCheck()
    {
        $file = $this->prophesize(PhpFileInfo::class);
        $file = $file->reveal();

        $ruleSet = $this->prophesize(RuleSet::class);

        $concreteChecker = $this
            ->prophesize(ViolationCheckerInterface::class);
        $concreteChecker->check($file, $ruleSet)->willReturn([])->shouldBeCalled();

        $concreteCheckerTwo = $this
            ->prophesize(ViolationCheckerInterface::class);
        $concreteCheckerTwo->check($file, $ruleSet)->willReturn([])->shouldBeCalled();

        $concreteCheckerThree = $this
            ->prophesize(ViolationCheckerInterface::class);
        $concreteCheckerThree->check($file, $ruleSet)->willReturn([])->shouldBeCalled();

        $checker = new ComposedViolationChecker([
            $concreteChecker->reveal(),
            $concreteCheckerTwo->reveal(),
            $concreteCheckerThree->reveal(),
        ]);

        $checker->check($file, $ruleSet->reveal());
    }

    public function testCheckReturnsArrayIfAnExceptionIsThrown()
    {
        $file = $this->prophesize(PhpFileInfo::class);
        $file = $file->reveal();

        $ruleSet = $this->prophesize(RuleSet::class);

        $concreteChecker = $this
            ->prophesize(ViolationCheckerInterface::class);
        $concreteChecker->check($file, $ruleSet)->willThrow(new \Exception())->shouldBeCalled();

        $checker = new ComposedViolationChecker([
            $concreteChecker->reveal(),
        ]);

        $this->assertSame([], $checker->check($file, $ruleSet->reveal()));
    }
}
