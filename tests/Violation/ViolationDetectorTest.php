<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ViolationCheckerInterface;
use SensioLabs\DeprecationDetector\Violation\ViolationDetector;
use SensioLabs\DeprecationDetector\Violation\ViolationFilter\ViolationFilterInterface;

class ViolationDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $violationChecker = $this->prophesize(
            ViolationCheckerInterface::class
        );
        $violationFilter = $this->prophesize(
            ViolationFilterInterface::class
        );
        $violationDetector = new ViolationDetector(
            $violationChecker->reveal(),
            $violationFilter->reveal()
        );

        $this->assertInstanceOf(ViolationDetector::class, $violationDetector);
    }

    public function testGetViolations()
    {
        $violation = $this->prophesize(Violation::class)->reveal();
        $filteredViolation = $this->prophesize(Violation::class)->reveal();

        $expected = array($violation);
        $phpFileInfo = $this->prophesize(PhpFileInfo::class)->reveal();
        $ruleSet = $this->prophesize(RuleSet::class)->reveal();

        $violationChecker = $this->prophesize(
            ViolationCheckerInterface::class
        );

        $violationChecker
            ->check($phpFileInfo, $ruleSet)
            ->willReturn(array($violation, $filteredViolation))
            ->shouldBeCalled();

        $violationFilter = $this->prophesize(
            ViolationFilterInterface::class
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
