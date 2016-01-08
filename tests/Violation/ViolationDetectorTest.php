<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation;

use SensioLabs\DeprecationDetector\Violation\ViolationDetector;

class ViolationDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $violationChecker = $this->prophesize(
            'SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface'
        );
        $violationFilter = $this->prophesize(
            'SensioLabs\DeprecationDetector\Violation\ViolationFilter\ViolationFilterInterface'
        );
        $violationDetector = new ViolationDetector(
            $violationChecker->reveal(),
            $violationFilter->reveal()
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\ViolationDetector', $violationDetector);
    }

    public function testGetViolations()
    {
        $violation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation')->reveal();
        $filteredViolation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation')->reveal();

        $expected = array($violation);
        $phpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo')->reveal();
        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet')->reveal();

        $violationChecker = $this->prophesize(
            'SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface'
        );

        $violationChecker
            ->check($phpFileInfo, $ruleSet)
            ->willReturn(array($violation, $filteredViolation))
            ->shouldBeCalled();

        $violationFilter = $this->prophesize(
            'SensioLabs\DeprecationDetector\Violation\ViolationFilter\ViolationFilterInterface'
        );
        $violationFilter->isViolationFiltered($violation)->willReturn(false)->shouldBeCalled();
        $violationFilter->isViolationFiltered($filteredViolation)->willReturn(true)->shouldBeCalled();

        $violationDetector = new ViolationDetector(
            $violationChecker->reveal(),
            $violationFilter->reveal()
        );

        $this->assertEquals(
            $expected,
            $violationDetector->getViolations($ruleSet, array($phpFileInfo))
        );
    }
}
