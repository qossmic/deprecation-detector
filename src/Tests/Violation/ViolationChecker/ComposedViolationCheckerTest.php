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

        $concreteChecker = $this
            ->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface');
        $concreteChecker->check($file)->willReturn(array())->shouldBeCalled();

        $concreteCheckerTwo = $this
            ->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface');
        $concreteCheckerTwo->check($file)->willReturn(array())->shouldBeCalled();

        $concreteCheckerThree = $this
            ->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface');
        $concreteCheckerThree->check($file)->willReturn(array())->shouldBeCalled();

        $checker = new ComposedViolationChecker(array(
            $concreteChecker->reveal(),
            $concreteCheckerTwo->reveal(),
            $concreteCheckerThree->reveal(),
        ));

        $checker->check($file);
    }

    public function testCheckReturnsArrayIfAnExceptionIsThrown()
    {
        $file = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $file = $file->reveal();

        $concreteChecker = $this
            ->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface');
        $concreteChecker->check($file)->willThrow(new \Exception())->shouldBeCalled();

        $checker = new ComposedViolationChecker(array(
            $concreteChecker->reveal(),
        ));

        $this->assertSame([], $checker->check($file));
    }
}
