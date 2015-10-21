<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ComposedViolationChecker;

class ComposedViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $checker = new ComposedViolationChecker(array());

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\ViolationChecker\ComposedViolationChecker',
            $checker
        );
    }

    public function testCheck()
    {
        $file = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $file = $file->reveal();

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');

        $concreteChecker = $this
            ->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface');
        $concreteChecker->check($file, $ruleSet)->willReturn(array())->shouldBeCalled();

        $concreteCheckerTwo = $this
            ->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface');
        $concreteCheckerTwo->check($file, $ruleSet)->willReturn(array())->shouldBeCalled();

        $concreteCheckerThree = $this
            ->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface');
        $concreteCheckerThree->check($file, $ruleSet)->willReturn(array())->shouldBeCalled();

        $checker = new ComposedViolationChecker(array(
            $concreteChecker->reveal(),
            $concreteCheckerTwo->reveal(),
            $concreteCheckerThree->reveal(),
        ));

        $checker->check($file, $ruleSet->reveal());
    }

    public function testCheckReturnsArrayIfAnExceptionIsThrown()
    {
        $file = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $file = $file->reveal();

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');

        $concreteChecker = $this
            ->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface');
        $concreteChecker->check($file, $ruleSet)->willThrow(new \Exception())->shouldBeCalled();

        $checker = new ComposedViolationChecker(array(
            $concreteChecker->reveal(),
        ));

        $this->assertSame(array(), $checker->check($file, $ruleSet->reveal()));
    }
}
