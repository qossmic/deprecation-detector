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
}
