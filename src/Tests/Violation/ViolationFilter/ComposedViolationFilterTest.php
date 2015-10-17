<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationFilter;

use SensioLabs\DeprecationDetector\Violation\ViolationFilter\ComposedViolationFilter;

class ComposedViolationFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterNoFilters()
    {
        $composedViolationFilter = new ComposedViolationFilter(array());
        $violation = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\Violation');
        $this->assertFalse($composedViolationFilter->violationIsFiltered($violation->reveal()));
    }

    public function testFilterOneFiltering()
    {
        $violation = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\Violation');
        $violationFilter = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\ViolationFilter\ViolationFilterInterface');
        $violationFilter->violationIsFiltered($violation->reveal())->willReturn(true);
        $composedViolationFilter = new ComposedViolationFilter(array($violationFilter->reveal()));
        $this->assertTrue($composedViolationFilter->violationIsFiltered($violation->reveal()));
    }

    public function testFilterOneNotFiltering()
    {
        $violation = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\Violation');
        $violationFilter = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\ViolationFilter\ViolationFilterInterface');
        $violationFilter->violationIsFiltered($violation->reveal())->willReturn(false);
        $composedViolationFilter = new ComposedViolationFilter(array($violationFilter->reveal()));
        $this->assertFalse($composedViolationFilter->violationIsFiltered($violation->reveal()));
    }

    public function testFilterOneNotFilteringAndOneFiltering()
    {
        $violation = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\Violation');
        $violationFilter1 = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\ViolationFilter\ViolationFilterInterface');
        $violationFilter1->violationIsFiltered($violation->reveal())->willReturn(false);
        $violationFilter2 = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\ViolationFilter\ViolationFilterInterface');
        $violationFilter2->violationIsFiltered($violation->reveal())->willReturn(true);
        $composedViolationFilter = new ComposedViolationFilter(array($violationFilter1->reveal(), $violationFilter2->reveal()));
        $this->assertTrue($composedViolationFilter->violationIsFiltered($violation->reveal()));
    }
}
